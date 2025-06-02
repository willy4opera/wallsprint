/**
 * Created by me664 on 2/4/15.
 */
jQuery(document).ready(function($){
    var import_debug=$('#import_debug');
   $('.btn_stp_do_import').click(function(){
       var comf = confirm ('Note: Importing data is recommended on fresh installs only once. Importing on sites with content or importing twice will duplicate menus, pages and all posts. Click OK to start !.');
       if(comf == true){
           $('.import_debug-css').addClass('active');

           import_debug.html('<h3>Data import is in progress, please do not reset your browser.</h3> <br><img width="20" height="20" class="loading_import" src="'+svp_importer.loading_src+'">');
           function start_loop_import(url){
               $.ajax({
                   url: url,
                   type: "POST",
                   dataType: "json",
                   success:function(html){
                       if(html){
                           
                           if(html.messenger){

                               import_debug.append(html.messenger);
                           } console.log(html.status);
                            //if(html.status == 1){
                               $('.loading_import').remove();
                               import_debug.append('<img width="20" height="20" class="loading_import" src="'+svp_importer.loading_src+'">')
                           //}

                           if(html.next_url != ""){
                               start_loop_import(html.next_url) ;
                           }else{
                               $('.loading_import').remove();
                           }

                           import_debug.scrollTop(import_debug[0].scrollHeight - import_debug.height());
                            $('.close-import').on('click',function(e){
                                e.preventDefault();
                                $('.import_debug-css').removeClass('active');
                           });
                       }
                   },
                   error:function(html){
                       //import_debug.append(html.responseText);
                       import_debug.append('<br><span class="red">Error: Stop Working</span><br><p>Are you having trouble installing the demo? Please <a href="https://bzotech.com/wordpress-theme-faqs/" target="_blank">CLICK HERE</a> to find the solution.</p><p>Note: You can import without media and import media separately. While importing media, you might get this error too. Please try to import several times until it successes.</p><br><a class="close-import bt-done" href="#">Close</a>');
                       import_debug.scrollTop(import_debug[0].scrollHeight - import_debug.height());
                        $('.close-import').on('click',function(e){
                            e.preventDefault();
                            $('.import_debug-css').removeClass('active');
                       });
                   }
               });
           }
           // start fist
           start_loop_import( $(this).data('url') );
       }
   });

});