
$ (document).ready (function () {
    $ ('#grouplist').DataTable ({
      processing: true,
      order: [[0, 'desc']],
    //   lengthMenu: [5, 10, 25, 50, 75, 100],
    });

    $ ('#exdate').flatpickr ({
        monthSelectorType: 'static',
      });

      $ ('#end_exdate').flatpickr ({
        monthSelectorType: 'static',
      });


  });