jQuery(document).ready(function($) {

    $('.action_delete_leads').on('click', function(){
        $.ajax({
            url : '/wp-admin/admin-ajax.php', //ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: $('.leads_list input[type=checkbox]:checked.select_lead, .leads_list input[type=hidden]'),
            success:function(data){
                if(data.status){
                    $.each(data.delete, function(i, lead_id){
                        $('#select-all-'+lead_id).parents('tr').remove();
                    })
                }
            }
        });
    });

    $('#select-all').on('change', function(){
        $('.select_lead').prop('checked', $(this).is(':checked'));
    });

    $('form #export').on('click', function(e){
        e.preventDefault();
        var button = $(this);
        var data = button.parents('form').serializeArray();
        button.append('<span class="dashicons dashicons-update spin margin-left-5"></span>');
        $('form .result').removeClass('text_green').removeClass('text_red');
        $('.result').text('');
        $.ajax({
            url : '/wp-admin/admin-ajax.php', //ajaxurl,
            type: 'POST',
            dataType: 'json',
            data:data,
            success:function(data){
                if(data.status){
                    $('form .result').addClass('text_green');
                }else{
                    $('form .result').addClass('text_red');
                }
                $('.button .dashicons').remove();
                $('form .result').text(data.message);
            }
        });

    });

    $('.form_tags span').on('click', function(){
        var tag = '{{'+$(this).text()+'}}';
        console.log(tag);
        var container = $(this).parents('tr').find('input, textarea');
        container.val( container.val() + tag );
    });

    $('.newtag').on('click', function(){
        var newTag = '<tr> <th></th> <td><input type="text" name="cf7-amocrm[tags][]" value="" placeholder="Введите тег"></td> <td><div class="button button-primary button-large removetag">Удалить</div></td> </tr>';
        $(this).parents('tr').before(newTag);
    });

    $('.new_utm').on('click', function(){
        lat_umt_id++;
        var new_utm  = '<div class="row">';
                new_utm += '<div class="value">';
                    new_utm += '<input type="text" name="amocrm_wc[utms]['+lat_umt_id+'][name]" value="custom">';
                new_utm += '</div>';
                new_utm += '<div class="value">';
                    new_utm += '<select name="amocrm_wc[utms]['+lat_umt_id+'][field_id]">';
                    $.each(amo_fields, function(field_id, field_name){
                        new_utm += '<option value="'+field_id+'">'+field_id+'</option>';
                    });
                    new_utm += '</select>';
                new_utm += '</div>';
                new_utm += '<div class="value"><span class="remove">Удалить</span></div>';
            new_utm += '</div>';
            $('.utms').append(new_utm);
    });
    

    $(document).on('click', '.removetag', function(){
        $(this).parents('tr').remove();
    });

    changePipeline(pipeline_id, pipeline_status);

    $('#pipeline_id').on('change', function(){
        var pipeline_id = $(this).val(); 
        changePipeline(pipeline_id, pipeline_status);
    });``
    
    function changePipeline(pipeline_id = false, status = false){
        if(!pipeline_id){
            return false;
        }
        var options = '<option value="0">Не выбрано</option>';
        if( (pipeline_id in pipelines) && ('statuses' in pipelines[pipeline_id]) ){
            options += '<option value="unsortered_'+pipeline_id+'">Неразобранное</option>';
            $.each(pipelines[pipeline_id]['statuses'], function(status_id, value) {
                options += '<option value="'+status_id+'">'+value.name+'</option>'
            }); 
        }


        $('#p_status').html(options);

        if(status){
            $('#p_status option[value='+status+']').attr('selected', 'selected');
            console.log(status);
        }

        $('#p_status').trigger("chosen:updated");
        
    }

    $('select').chosen({
        disable_search_threshold: 10,
        width: '100%',
        allow_single_deselect: true,
        no_results_text: 'Поиск не дал результатов: '
    });


});