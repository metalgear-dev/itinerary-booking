$(document).ready(function() {
   $('#btnAddLocation').on('click', function() {
      $frm = $('#frmLocation');
      var location_id = $('#selectLocation').val();
      if (location_id && location_id > 0) {
          $frm.find("input[name='location_id']").val(location_id);
          $frm.find("input[name='action']").val('add');
          $frm.submit();
      } else {
          return false;
      }
   });
   $('#btnAddTheme').on('click', function() {
      $frm = $('#frmTheme');
      var theme_id = $('#selectTheme').val();
      if (theme_id && theme_id > 0) {
          $frm.find("input[name='theme_id']").val(theme_id);
          $frm.find("input[name='action']").val('add');
          $frm.submit();
      } else {
          return false;
      }
   });
   $('.remove-theme').on('click', function() {
       var theme_id = $(this).data('id');
       if (confirm('ジャンルを本当に削除しますか？')) {
           $frm = $('#frmTheme');
           $frm.find("input[name='theme_id']").val(theme_id);
           $frm.find("input[name='action']").val('remove');
           $frm.submit();
       } else {
           return false;
       }
   });
    $('.remove-location').on('click', function() {
        var location_id = $(this).data('id');
        if (confirm('エリアを本当に削除しますか？')) {
            $frm = $('#frmLocation');
            $frm.find("input[name='location_id']").val(location_id);
            $frm.find("input[name='action']").val('remove');
            $frm.submit();
        } else {
            return false;
        }
    });
});
