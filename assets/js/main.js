// Add any JavaScript functionality here
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for delete actions
    // const deleteButtons = document.querySelectorAll('.btn-delete');
    // deleteButtons.forEach(button => {
    //     button.addEventListener('click', function(e) {
    //         const confirmed = confirm('Are you sure you want to delete this item?');
    //         if (!confirmed) {
    //             e.preventDefault();
    //         }
    //     });
    // });
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});