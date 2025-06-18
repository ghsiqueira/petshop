// Funções para o sistema de petshop
document.addEventListener('DOMContentLoaded', function() {
    // Habilitar todos os tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Ocultar mensagens de alerta após 5 segundos, exceto o warningAlert
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            // Se o alerta for o #warningAlert, não fecha automaticamente
            if (alert.id === 'warningAlert') return;

            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
