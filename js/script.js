$('a.delete').on('click', e => {
    e.preventDefault();
    if (confirm('Are you sure?')) {
        /* this method returns true if the user clicked ok false otherwise.*/
        var frm = $("<form>");
        frm.attr('method', 'post');
        frm.attr('action', $(this).attr('href'));
        frm.appendTo("body");
        frm.submit();
        /* jquery form creation and submission */
    }

});


$.validator.addMethod("dateTime", function (value, element) {
    return (value == "") || !isNaN(Date.parse(value));
}, "Must be a valid date and time");
/* we added our own validation above*/

//this below is how we validate data
$("#formArticle").validate({
    rules: {
        title: {
            required: true
        },
        content: {
            required: true
        },
        published_at: {
            dateTime: true
        }
    }
});

$("button.publish").on("click", function (e) {
    console.log('whatt')
    const id = $(this).data('id');
    /* the this here represents the button object. and we use the
     data method on it and as argument we pass in the attribute data we want to grab */

    const button = $(this);
    /* we store the button object in this variable so that we can use it down below */

    $.ajax({
        url: '/admin/publish-article.php',
        type: 'POST', //method
        data: { id: id }// request body
    })
        .done(function (data) {
            button.parent().html(data);
            /*  
            we use the button object as a base to apply jquery methods on it. like so.
             we display the sent data once it has gone through
            */
        }).fail(function (data) {
            /* jquery error handling.. */
            alert("An error occurred");

        });;
});

/**
 * Validate the contact form
 */
$("#formContact").validate({
    rules: {
        email: {
            required: true,
            email: true
        },
        subject: {
            required: true
        },
        message: {
            required: true
        }
    }
});
