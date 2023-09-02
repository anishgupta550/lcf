<script>  
$(document).ready(function(){
	
	$('#post_form').on('submit', function(event){
        event.preventDefault();
        if($('#post_type').val() == 'upload')
        {
            $('#post_content').val($('#sub_division').html());
        }

        if($('#post_type').val() == 'link')
        {
            $('#post_content').val($('#link_content').html());
            $('#link_content').css('padding', '0');
            $('#link_content').css('background-color', '');
            $('#link_content').css('margin-bottom', '0');
        }

        if($('#post_content').val() == '')
        {
            alert('Enter Story Content');
        }
        else
        {
            var form_data = $(this).serialize();
            $.ajax({
                url:"action.php",
                method:"POST",
                data:form_data,
                beforeSend:function()
                {
                    $('#share_post').attr('disabled', 'disabled');  
                },
                success:function(data)
                {
                    //alert('Post has been shared');
                    $('#dynamic_field').html('<textarea name="post_content" id="post_content" maxlength="160" class="form-control" placeholder="Write your short story"></textarea>');
                    $('#post_type').val('text');
                    $('#post_form')[0].reset();
                    fetch_post();
                    $('#link_content').html('');
                    $('#share_post').attr('disabled', false);
                }
            })
        }
    });

    fetch_post();

    function fetch_post()
    {
       var action = 'fetch_post';
       $.ajax({
            url:'action.php',
            method:"POST",
            data:{action:action},
            success:function(data)
            {
                $('#post_list').html(data);
            }
       })
    }

    fetch_user();

    function fetch_user()
    {
        var action = 'fetch_user';
        $.ajax({
            url:"action.php",
            method:"POST",
            data:{action:action},
            success:function(data)
            {
                $('#user_list').html(data);
            }
        });
    }

    $(document).on('click', '.action_button', function(){
        var sender_id = $(this).data('sender_id');
        var action = $(this).data('action');
        $.ajax({
            url:"action.php",
            method:"POST",
            data:{sender_id:sender_id, action:action},
            success:function(data)
            {
                fetch_user();
                fetch_post();
            }
        })
    });

    var post_id;
    var user_id;

    $(document).on('click', '.post_comment', function(){
        post_id = $(this).attr('id');
        user_id = $(this).data('user_id');
        var action = 'fetch_comment';
        $.ajax({
            url:"action.php",
            method:"POST",
            data:{post_id:post_id, user_id:user_id, action:action},
            success:function(data){
                $('#old_comment'+post_id).html(data);
                $('#comment_form'+post_id).slideToggle('slow');
            }
        })
        
    });

    $(document).on('click', '.submit_comment', function(){
        var comment = $('#comment'+post_id).val();
        var action = 'submit_comment';
        var receiver_id = user_id;
        if(comment != '')
        {
            $.ajax({
                url:"action.php",
                method:"POST",
                data:{post_id:post_id,receiver_id:receiver_id,comment:comment,action:action},
                success:function(data)
                {
                    $('#comment_form'+post_id).slideUp('slow');
                    fetch_post();
                }
            })
        }
    });

    $(document).on('click', '.repost', function(){
        var post_id = $(this).data('post_id');
        var action = 'repost';
        $.ajax({
            url:"action.php",
            method:"POST",
            data:{post_id:post_id, action:action},
            success:function(data)
            {
                alert(data);
                fetch_post();
            }
        })
    });

    $(document).on('click', '.like_button', function(){
        var post_id = $(this).data('post_id');
        var action = 'like';
        $.ajax({
            url:"action.php",
            method:"POST",
            data:{post_id:post_id, action:action},
            success:function(data)
            {
                alert(data);
                fetch_post();
            }
        })
    });

    $('body').tooltip({
        selector: '.like_button',
        title: fetch_post_like_user_list,
        html: true,
        placement: 'right'
    });

    function fetch_post_like_user_list()
    {
        var fetch_data = '';
        var element = $(this);
        var post_id = element.data('post_id');
        var action = 'like_user_list';
        $.ajax({
            url:"action.php",
            method:"POST",
            async: false,
            data:{post_id:post_id, action:action},
            success:function(data)
            {
                fetch_data = data;
            }
        });
        return fetch_data;
    }

    $('#uploadFile').on('change', function(event){
        var html = '<div class="main_division">';
        html += '<div id="sub_division" contenteditable class="form-control"></div></div>';
        html += '<input type="hidden" name="post_content" id="post_content" />';
        $('#post_type').val('upload');
        $('#dynamic_field').html(html);
        $('#uploadImage').ajaxSubmit({
            target :'#sub_division',
            resetForm :true
        });
    });

    $(document).on('keyup', '#post_content', function(){
        var check_content = $('#post_content').val();
        var check_url = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;

        var if_url = check_content.match(check_url);

        if(if_url)
        {
            $('#link_content').css('padding', '16px');
            $('#link_content').css('background-color', '#f9f9f9');
            $('#link_content').css('margin-bottom', '16px');
            $('#link_content').html('<h4>Fetching...</h4>');
            $('#post_type').val('link');
            var action = 'fetch_link_content';
            $.ajax({
                url:"action.php",
                method:"POST",
                data:{action:action, url:if_url},
                success:function(data)
                {
                    var title = $(data).filter("meta[property='og:title']").attr('content');
                    var description = $(data).filter("meta[property='og:description']").attr('content');

                    var image = $(data).filter("meta[property='og:image']").attr('content');

                    if(title == undefined)
                    {
                        title = $(data).filter("meta[name='twitter:title']").attr('content');
                    }

                    if(description == undefined)
                    {
                        description = $(data).filter("meta[name='twitter:description']").attr('content');
                    }

                    if(image == undefined)
                    {
                        image = $(data).filter("meta[name='twitter:image']").attr('content');
                    }

                    var output = '<p><a href="'+if_url[0]+'">'+if_url[0]+'</a></p>';

                    output += '<img src="'+image+'" class="img-responsive img-thumbnail" />';
                    output += '<h3><b>'+title+'</b></h3>';
                    output += '<p>'+description+'</p>';
                    $('#link_content').html(output);
                }
            })
        }
        else
        {
            $('#link_content').html('');
            $('#link_content').css('padding', '0');
            $('#link_content').css('background-color', '');
            $('#link_content').css('margin-bottom', '');
            return false;
        }
    });

    $('#view_notification').click(function(){
        var action = 'update_notification_status';
        $.ajax({
            url:"action.php",
            method:"post",
            data:{action:action},
            success:function(data)
            {
                $('#total_notification').remove();
            }
        })
    });
	
});  
</script>