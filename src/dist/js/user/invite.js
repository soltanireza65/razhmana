$('#invite-copy').click(function () {

    // shareDialog.classList.add('is-open');
    copyToClipboard($(this).data('invite-code'))

    function copyToClipboard(text) {
        /* Create a textarea element to hold the text to be copied */
        var textarea = document.createElement("textarea");
        textarea.value = text; // Set the text to be copied as the parameter
        document.body.appendChild(textarea); // Append the textarea element to the DOM
        textarea.select(); // Select the text in the textarea
        document.execCommand("copy"); // Execute the copy command
        document.body.removeChild(textarea); // Remove the textarea element from the DOM
        $('.mj-share-alert').fadeIn(300);
        setTimeout(function () {
            $('.mj-share-alert').fadeOut(200);
        }, 2000)
    }

});
$('#share').click(function () {
    if (navigator.share) {
        navigator.share({
            title: lang_vars.invite_share_text ,
            text: lang_vars.invite_share_text,
            url: 'https://ntirapp.com/login/'+this.dataset.inviteCode
        })
            .then(() => {
                // console.log('Thanks for sharing!');
            })
            .catch((e) => {
                console.log(e);
            });

    } else {
        // shareDialog.classList.add('is-open');
        copyToClipboard($(this).data('invite-code'))

        function copyToClipboard(text) {
            /* Create a textarea element to hold the text to be copied */
            var textarea = document.createElement("textarea");
            textarea.value = text; // Set the text to be copied as the parameter
            document.body.appendChild(textarea); // Append the textarea element to the DOM
            textarea.select(); // Select the text in the textarea
            document.execCommand("copy"); // Execute the copy command
            document.body.removeChild(textarea); // Remove the textarea element from the DOM
            $('.mj-share-alert').fadeIn(300);
            setTimeout(function () {
                $('.mj-share-alert').fadeOut(200);
            }, 2000)
        }
    }
})