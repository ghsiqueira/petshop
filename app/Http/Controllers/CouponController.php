<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|petshop')->except(['apply', 'remove']);
    }

    public function index(Request $request)
    {
        $query = Coupon::with(['petshop', 'creator']);

        // Se for petshop, mostrar apenas seus cupons
        if (auth()->user()->hasRole('petshop')) {
            $petshop = auth()->user()->petshop;
            $query->where('petshop_id', $petshop->id);
        }

        // Filtros
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->valid();
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', Carbon::now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ], [
            'code.unique' => 'Este código de cupom já existe.',
            'expires_at.after' => 'A data de expiração deve ser posterior à data de início.',
        ]);

        // Validações específicas por tipo
        if ($request->type === 'percentage') {
            if ($request->value > 100) {
                return back()->withErrors(['value' => 'O valor da porcentagem não pode ser maior que 100%']);
            }
        }

        $couponData = $request->except(['_token']);
        $couponData['code'] = strtoupper($request->code); // Código sempre maiúsculo
        $couponData['created_by'] = auth()->id();

        // Se for petshop, definir petshop_id
        if (auth()->user()->hasRole('petshop')) {
            $couponData['petshop_id'] = auth()->user()->petshop->id;
        }

        Coupon::create($couponData);

        return redirect()->route('coupons.index')
                        ->with('success', 'Cupom criado com sucesso!');
    }

    public function show(Coupon $coupon)
    {
        // Verificar permissão
        if (auth()->user()->hasRole('petshop') && $coupon->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }

        $coupon->load(['usages.user', 'usages.order']);
        
        return view('coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        // Verificar permissão
        if (auth()->user()->hasRole('petshop') && $coupon->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }

        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        // Verificar permissão
        if (auth()->user()->hasRole('petshop') && $coupon->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        // Validações específicas por tipo
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'O valor da porcentagem não pode ser maior que 100%']);
        }

        $couponData = $request->except(['_token', '_method']);
        $couponData['code'] = strtoupper($request->code);

        $coupon->update($couponData);

        return redirect()->route('coupons.index')
                        ->with('success', 'Cupom atualizado com sucesso!');
    }

    public function destroy(Coupon $coupon)
    {
        // Verificar permissão
        if (auth()->user()->hasRole('petshop') && $coupon->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }

        // Verificar se o cupom foi usado
        if ($coupon->used_count > 0) {
            return back()->with('error', 'Não é possível excluir um cupom que já foi utilizado.');
        }

        $coupon->delete();

        return redirect()->route('coupons.index')
                        ->with('success', 'Cupom excluído com sucesso!');
    }

    // Método para aplicar cupom no carrinho (AJAX)
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code));
        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Seu carrinho está vazio.'
            ]);
        }

        // Buscar cupom
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom não encontrado.'
            ]);
        }

        // Verificar se é válido
        if (!$coupon->canBeUsedBy(auth()->id())) {
            $message = 'Cupom inválido';
            
            if (!$coupon->isValid()) {
                if ($coupon->expires_at && Carbon::now()->gt($coupon->expires_at)) {
                    $message = 'Este cupom expirou.';
                } elseif (!$coupon->is_active) {
                    $message = 'Este cupom não está ativo.';
                } elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    $message = 'Este cupom atingiu o limite de uso.';
                }
            } else {
                $userUsages = $coupon->usages()->where('user_id', auth()->id())->count();
                if ($userUsages >= $coupon->usage_limit_per_user) {
                    $message = 'Você já utilizou este cupom o máximo de vezes permitido.';
                }
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }

        // Calcular total do carrinho
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Calcular desconto
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount == 0) {
            return response()->json([
                'success' => false,
                'message' => $coupon->minimum_amount 
                    ? 'Valor mínimo de R$ ' . number_format($coupon->minimum_amount, 2, ',', '.') . ' não atingido.'
                    : 'Cupom não pode ser aplicado a este pedido.'
            ]);
        }

        // Armazenar cupom na sessão
        session([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount' => $discount,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cupom aplicado com sucesso!',
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount' => $discount,
                'discount_formatted' => 'R$ ' . number_format($discount, 2, ',', '.'),
            ],
            'totals' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $subtotal - $discount,
                'subtotal_formatted' => 'R$ ' . number_format($subtotal, 2, ',', '.'),
                'total_formatted' => 'R$ ' . number_format($subtotal - $discount, 2, ',', '.'),
            ]
        ]);
    }

    // Método para remover cupom do carrinho (AJAX)
    public function remove()
    {
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Cupom removido com sucesso!'
        ]);
    }

    public function toggle(Coupon $coupon)
    {
        // Verificar permissão
        if (auth()->user()->hasRole('petshop') && $coupon->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }

        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        $status = $coupon->is_active ? 'ativado' : 'desativado';

        return back()->with('success', "Cupom {$status} com sucesso!");
    }
}