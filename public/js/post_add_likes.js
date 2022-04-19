$(document).ready(function(){
    $("#addPostLikes").on("click", function(event){
        var post = $("#addPostLikes").attr('data-post');
        $.ajax({
            url:        '/post/likes/'+post,
            type:       'POST',
            dataType:   'json',
            async:      true,

            success: function(data, status) {
                var json = JSON.parse(data.data);
                $('#post-likes').html(json.likeCount);
                if(json.likeStatus) {
                    $( "#addPostLikes" ).removeClass( "btn-info" ).addClass( "btn-danger" ).html( "Delete like" );
                } else {
                    $( "#addPostLikes" ).removeClass( "btn-danger" ).addClass( "btn-info" ).html( "Add like" );
                }

            },
            error : function(xhr, textStatus, errorThrown) {
                console.log('Ajax request failed.');
            }
        });
    });
});