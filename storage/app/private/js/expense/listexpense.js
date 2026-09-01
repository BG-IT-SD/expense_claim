(function ($) {
  function getCsrfToken() {
    var token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
  }

  function initExpenseTable() {
    var $table = $('#ExpenseList');

    if (!$table.length || !$.fn.DataTable || $.fn.DataTable.isDataTable($table[0])) {
      return;
    }

    $table.DataTable({
      order: [],
      responsive: true,
      pageLength: 10,
      language: {
        search: '',
        searchPlaceholder: 'Search...'
      }
    });
  }

  window.cancelExpense = function (id) {
    if (!id) {
      return;
    }

    Swal.fire({
      title: 'Confirm cancel?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: window.location.origin + '/Expense/' + id,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken()
        },
        success: function (response) {
          Swal.fire({
            title: response.message || 'Success',
            icon: response.status === 'success' ? 'success' : 'error',
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          }).then(function () {
            window.location.reload();
          });
        },
        error: function (xhr) {
          var message = xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message
            : 'Unable to cancel expense.';

          Swal.fire({
            title: message,
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
        }
      });
    });
  };

  $(initExpenseTable);
})(jQuery);
