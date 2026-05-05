<section class="contacto p-sm-5 px-4 py-5" id="info">
    <div class="container pt-sm-4">
        <div class="row px-sm-3">
			<div class="col-sm-6 col-12 pl-sm-5 pr-sm-4">
                <div class="card formu px-sm-3 pt-4 pt-sm-0">
				    <h3 class="pl-sm-4 pt-sm-4 mb-sm-3 mb-4">Contacto</h3>
                    <?php echo do_shortcode('[contact-form-7 id="d3f6ebb" title="Formulario Contacto"]'); ?>
                </div>
			</div>
		    <div class="col-sm-6 col-12 pr-sm-5 pl-sm-4 d-none d-sm-block">
                <?php $img_contact = get_field('img_contact', 'option');
                    if ($img_contact) : ?>
				    <div class="card info mb-sm-4">
                        <img src="<?php echo esc_url($img_contact); ?>" class="img-fluid" alt="<?php echo esc_attr( sprintf( __( 'Información de contacto — %s', 'theme_skema' ), get_bloginfo( 'name' ) ) ); ?>">
                    </div>
                <?php endif; ?>
                <div class="card info-redes mt-4 p-4">
                    <?php $logo_marca = get_field('logo_marca', 'option');
                        if ($logo_marca) : ?>
                        <div class="logo-info-skema py-3">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
                                <img width="472" height="91" src="<?php echo esc_url($logo_marca); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url($logo_marca); ?> 472w,<?php echo esc_url($logo_marca); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="redes-info py-3">
                        <?php if(get_field('face', 'option')): ?>
                            <a href="<?php the_field('face', 'option') ?>" target="_blank" rel="noopener noreferrer"
                                aria-label="<?php echo esc_attr__( 'Facebook', 'theme_skema' ); ?>"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if(get_field('inst', 'option')): ?>
				            <a href="<?php the_field('inst', 'option') ?>" target="_blank" rel="noopener noreferrer"
                                aria-label="<?php echo esc_attr__( 'Instagram', 'theme_skema' ); ?>"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if(get_field('wp', 'option')): ?>
				            <a href="https://wa.me/<?php the_field('wp', 'option') ?>" target="_blank" rel="noopener noreferrer"
                                aria-label="<?php echo esc_attr__( 'Contactar por WhatsApp', 'theme_skema' ); ?>"><i class="bi bi-whatsapp"></i></a>
                        <?php endif; ?>
                        <?php if(get_field('tele', 'option')): ?>
                            <a href="tel:<?php the_field('tele', 'option') ?>"
                                aria-label="<?php echo esc_attr__( 'Llamar por teléfono', 'theme_skema' ); ?>"><i class="bi bi-phone"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
			</div>
		</div>
	</div>
</section> 