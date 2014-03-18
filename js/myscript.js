$(function(){
    var editor = $('.editor');

    // show editor
    $('.add_comment').each(function(){        
        var this_=$(this);        
        var id = this_.attr('id');
        this_.click(function(){            
            if($('#all-comment'+id).length>0){
                editor.insertAfter("div#all-comment"+id).show('fast');
                $("input[name=parent_id]").val(id);
            }
            else{
                editor.insertAfter(".header").show('fast');
                 $("input[name=parent_id]").val(0);
            }            
        });
    });   

    //Edit
    $('.edit').click(function(){
            var this_ = $(this);
            var id =this_.attr('id');
            var gid = id.substr(1,2);            
            editor.insertAfter("div#all-comment"+gid).show("fast");
            var auth = $("#auth"+gid).text();
            var comment = $("#com"+gid).text();
            $("input[name=name]").val(auth);
            $("textarea[name=comment]").val(comment);
            $("input[name=parent_id]").val(gid);
            $("input[type=submit]").val("Edit");
            $("input[type=submit]").attr('name','edit');           
       });    

    // delete
    $('.delete').click(function(){               
        var id = $(this).attr("id");
        var id_parent = $(this).parents("li");
        var gid = id_parent.attr("id");        
        var parent = $(this).parents("li#"+gid);
        $.ajax({
            url: "/ajaxpost.php",
            type: "POST",
            dataType: "json",
            data: {
                    action : 'delete',
                    id : id
            },
            success: function(data){                
                if(data.result == "error"){
                    alert(data.message);
                }
                else{
                    parent.remove();
                    alert(data.message);
                    window.location.reload();
                }
            },
            error: function(){
                alert('error delete');
            }
        });
        return false;
    });    
});