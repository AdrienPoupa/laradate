<div class="well">
    <form action="{{ \App\Utils::getPollUrl($poll_id, false, '', 'send_edit_link', $editedVoteUniqueId) }}" method="POST" class="form-inline" id="send_edit_link_form">
        @csrf
        <p>@lang('editLink.If you don\'t want to lose your personalized link, we can send it to your email.')</p>
        <div class="form-group">
            <label for="email" class="control-label">@lang('pollinfo.Email')</label>
            <input type="email" name="email" id="email" class="form-control" />
            <input type="submit" id="send_edit_link_submit" value="@lang('editLink.Send')" class="btn btn-success">
        </div>
    </form>
    <div id="send_edit_link_alert"></div>
</div>

<script>
    $(document).ready(function () {

        var form = $('#send_edit_link_form');
        form.submit(function(event) {
            event.preventDefault();

            if ($('#email').val()) {
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(data)
                    {
                        var newMessage;
                        if (data.result) {
                            $('#send_edit_link_form').remove();
                            newMessage = $('#genericUnclosableSuccessTemplate').clone();
                        } else {
                            newMessage = $('#genericErrorTemplate').clone();
                        }
                        newMessage
                                .find('.contents')
                                .text(data.message.message);
                        newMessage.removeClass('hidden');
                        $('#send_edit_link_alert')
                                .empty()
                                .append(newMessage);
                    },
                    complete: function() {
                        $('#add_comment').removeAttr("disabled");
                    }
                });
            }

            return false;
        });
    });

</script>