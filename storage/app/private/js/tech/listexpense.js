(function ($) {
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

  $(initExpenseTable);
})(jQuery);
