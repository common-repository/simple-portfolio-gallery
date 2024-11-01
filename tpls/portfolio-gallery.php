<?php ?>
<div class="portfolio-image-gallery">
    <div class="container">

        <section class="ff-container">

            <div class="clr"></div>

            <ul class="ff-items">
                <?php
                $slides = new WP_Query("post_type=portfolios-gallery");
                if ($slides->have_posts()) {
                    while ($slides->have_posts()) {
                        $slides->the_post();
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'single-post-thumbnail');
                        ?>
                        <li class="ff-item-type-2 view view-tenth">
                            <figure>
                                <a href="#">
                                    <img src="<?php echo $image[0] ?>" />	
                                    <div class="mask">
                                        <h2><?php the_title() ?></h2>

                                        <a href="#" id="<?php echo get_the_ID() ?>" class="info portfolio-info" data-toggle="modal" data-target="#modal">Read More</a>
                                    </div>					
                                </a>	
                            </figure>				
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        </section>
    </div>

    <div class="modal fade" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h2 class="modal-title"></h2>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <a id="modal-demo-id" class="btn btn-primary modal-live-project" href="">Live Project</a>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
</div>

<script>
    jQuery(document).ready(function($) {

        $('.portfolio-info').on('click', function(e) {
            var pid = $(this).attr('id');
            $.ajax({
                type: 'POST',
                url: "index.php?task=show-portfolio&portfolio=" + pid,
                dataType: 'json',
                success: function(result) {
                    $('#modal .modal-title').html(result.title);
                    $('#modal #modal-demo-id').attr('href', result.live_url);
                    $('#modal .modal-body').css('background', '#ffffff').html("<div class='row text-center'><div class='col-md-12'><img src='"+result.url+"' width='350px' height='200px' /></div><div class='row'><div class='modal-content-body col-md-12'><p>"+result.content+"</p></div></div></div>").load();
                    $('#modal').modal('show');
                }});
            return false;
        });
        return false;

    });
</script>
