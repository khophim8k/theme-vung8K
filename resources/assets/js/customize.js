(function ($) {
    "user strict";
    var HT = {};

    HT.customFilter = function () {
        $(document).on("click", ".filter-button", function (e) {
            e.preventDefault();
    
            const $form = $(this).closest("form");
            const method = $form.attr("method") || "GET";
            const action = $form.attr("action") || window.location.href;
            const data = $form.serialize();
    
            $.ajax({
                url: action,
                method: method,
                data: data,
                success: function (res) {
                    if (res.status === true) {
                        $(".khoi-trai").html(res.html);
                    } else {
                        alert(res.message || "Không có kết quả.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert("Đã xảy ra lỗi. Vui lòng thử lại.");
                }
            });
        });
    };

    $(document).ready(function () {
        HT.customFilter();
    });
})(jQuery);
