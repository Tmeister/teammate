<?php
/*
	Section: Teammate
	Author: Enriue Chavez
	Author URI: http://enriquechavez.co
	Description: Teammate is a DMS section that allows you to show details for a company member or work team member. Every teammate box has up to 12 configuration options: Avatar, Name, Position, mini-bio, and up to 8 social media links. This section can be used to create a detailed "About Us", "Meet the team", or can even be used to create a "Testimonials" page.
	Class Name: TMTeammate
	Demo: http://dms.tmeister.net/teammate
	Version: 1.1
	Filter: misc
*/

class TMTeammate extends PageLinesSection {

    var $section_name      = 'Teammate';
    var $section_version   = '1.1';
    var $section_key ;
    var $chavezShop;

    function section_persistent()
    {
        $this->section_key = strtolower( str_replace(' ', '_', $this->section_name) );
        //$this->verify_license();
        //add_filter('pl_sorted_settings_array', array(&$this, 'add_global_panel'));
    }

    function verify_license(){
        if( !class_exists( 'chavezShopVerifier' ) ) {
            include( dirname( __FILE__ ) . '/inc/chavezshop_verifier.php' );
        }
        $this->chavezShop = new chavezShopVerifier( $this->section_name, $this->section_version, $this->opt('teammate_license_key') );
    }

    function add_global_panel($settings){
        $valid = "";
        if( get_option( $this->section_key."_activated" ) ){
            $valid = ( $this->chavezShop->check_license() ) ? ' - Your license is valid' : ' - Your license is invalid';
        }

        if( !isset( $settings['eChavez'] ) ){
            $settings['eChavez'] = array(
                'name' => 'Enrique Chavez Shop',
                'icon' => 'icon-shopping-cart',
                'opts' => array()
            );
        }

        $collapser_opts = array(
            'key'   => 'teammate_license_key',
            'type'  => 'text',
            'title' => '<i class="icon-shopping-cart"></i> ' . __('Teammate License Key', 'tmteammate') . $valid,
            'label' => __('License Key', 'tmteammate'),
            'help'  => __('The section is fully functional whitout a key license, this license is used only get access to autoupdates within your admin.', 'tmteammate')

        );

        array_push($settings['eChavez']['opts'], $collapser_opts);
        return $settings;

    }

    function section_styles(){
        wp_enqueue_script( 'teammate', $this->base_url . '/js/teammate.js', array( 'jquery' ), '1.0', true );
    }

    function section_head() {
    ?>
        <script>
            jQuery(document).ready(function($) {
                jQuery('<?php echo ".tab". $this->meta["clone"]?>').cicleSocials();
            });
        </script>

        <style>
            .tab<?php echo $this->meta['clone']?> .card .team-avatar,
            .tab<?php echo $this->meta['clone']?> .card .team-content,
            .tab<?php echo $this->meta['clone']?> .square .member-wrapper .member-avatar,
            .tab<?php echo $this->meta['clone']?> .circle .member-wrapper .member-avatar{
                background: <?php echo pl_hashify($this->opt('team_bg_img'))?>;
                border: 1px solid <?php echo pl_hashify($this->opt('team_bg_img_border'))?>;
            }
        </style>

    <?php
    }

   	function section_template(){

        $boxes = $this->opt('team_boxes');
        $layout = $this->opt('team_layout');

        if(PL_CORE_VERSION > '1.0.4'){
            //Draw new shit
            $team_array = $this->opt('team_array');
            $this->draw_new_boxes($team_array, $boxes, $layout);
        }else{
            //Draw old shit
            $this->old_drawer($boxes, $layout);
        }

    ?>
    <?php
    }

    function draw_new_boxes($team_array, $boxes, $layout){
        $upgrade_mapping = array(
            'name'      => 'team_m_name_%s',
            'position'  => 'team_m_position_%s',
            'image'     => 'team_m_image_%s',
            'external'  => 'team_m_external_%s',
            'bio'       => 'team_m_bio_%s',
            'facebook'  => 'facebook_url_%s',
            'github'    => 'github_url_%s',
            'google'    => 'google_url_%s',
            'linkedin'  => 'linkedin_url_%s',
            'pinterest' => 'pinterest_url_%s',
            'tumblr'    => 'tumblr_url_%s',
            'twitter'   => 'twitter_url_%s'
        );
        $team_array = $this->upgrade_to_array_format_from_zero('team_array', $team_array, $upgrade_mapping, $boxes);
        if( !is_array( $team_array)){
            echo setup_section_notify($this, __('Please start adding some teammates.', 'tmteammate'));
            return;
        }
        $id = 1;
        ob_start();
        ?>
            <div class="row tab<?php echo $this->meta['clone'] ?>">
                <?php foreach ($team_array as $teammate):
                    $teammate['image'] = pl_array_get('image', $teammate);
                ?>
                    <div class="span<?php echo $this->opt('team_span') ?>">
                        <?php
                            switch ($layout) {
                                case 'square':
                                    $this->draw_circles($teammate, 'square', $id);
                                    break;
                                case 'card':
                                    $this->draw_cards($teammate, 'card', $id);
                                    break;
                                case 'circle':
                                default:
                                    $this->draw_circles($teammate, 'circle', $id);
                            }
                        ?>
                    </div>
                <?php $id++; endforeach; ?>
            </div>
        <?php
        ob_end_flush();
    }

    function old_drawer($boxes, $layout){
        if( $boxes == false){
            echo setup_section_notify($this, __('Please start adding some teammates.', 'tmteammate'));
            return;
        }
        ob_start();
        ?>
            <div class="row tab<?php echo $this->meta['clone'] ?>">
                <?php
                    for ($i=0; $i<$boxes; $i++):
                        $teammate = array(
                            'name'      => $this->opt(sprintf('team_m_name_%s', $i)),
                            'position'  => $this->opt(sprintf('team_m_position_%s', $i)),
                            'image'     => $this->opt(sprintf('team_m_image_%s', $i)),
                            'external'  => $this->opt(sprintf('team_m_external_%s', $i)),
                            'bio'       => $this->opt(sprintf('team_m_bio_%s', $i)),
                            'facebook'  => $this->opt(sprintf('facebook_url_%s', $i)),
                            'github'    => $this->opt(sprintf('github_url_%s', $i)),
                            'google'    => $this->opt(sprintf('google_url_%s', $i)),
                            'linkedin'  => $this->opt(sprintf('linkedin_url_%s', $i)),
                            'pinterest' => $this->opt(sprintf('pinterest_url_%s', $i)),
                            'tumblr'    => $this->opt(sprintf('tumblr_url_%s', $i)),
                            'twitter'   => $this->opt(sprintf('twitter_url_%s', $i))
                        );
                ?>
                    <div class="span<?php echo $this->opt('team_span') ?>">
                        <?php
                            switch ($layout) {
                                case 'square':
                                    $this->draw_circles($teammate, 'square', $i);
                                    break;
                                case 'card':
                                    $this->draw_cards($teammate, 'card', $i);
                                    break;
                                case 'circle':
                                default:
                                    $this->draw_circles($teammate, 'circle', $i);
                            }
                        ?>
                    </div>
                <?php endfor ?>
            </div>
        <?php
        ob_end_flush();
    }


    function draw_cards($teammate, $main_class, $id){
        $image = $teammate['image'] ? $teammate['image'] : "http://dummyimage.com/100/4d494d/686a82.gif&text=100+x+100";
        $old = (PL_CORE_VERSION > '1.0.4') ? false : true;
        ob_start();
    ?>
        <div class="<?php echo $main_class ?>">
            <div class="team-item inner-<?php echo $main_class ?>">
                <div class="member-wrapper clear">
                    <div class="team-avatar">
                        <img data-sync="<?php echo $old ? 'team_m_image_'.$id : 'team_array_item'.$id.'_image' ?>" src="<?php echo $image ?>" alt="<?php echo $teammate['name'] ?>">
                    </div>
                    <div class="team-content">
                        <div class="member-title">
                            <h2>
                                <?php if (!$teammate['external']): ?>
                                    <span data-sync="<?php echo $old ? 'team_m_name_'.$id : 'team_array_item'.$id.'_name' ?>">
                                        <?php echo $teammate['name'] ? $teammate['name'] : 'Teammate '.($id+1); ?>
                                    </span>
                                <?php else: ?>
                                     <a href="<?php echo $this->opt('team_m_external_'.$id) ?>">
                                        <span data-sync="<?php echo $old ? 'team_m_name_'.$id : 'team_array_item'.$id.'_name' ?>">
                                            <?php echo $teammate['name'] ? $teammate['name'] : 'Teammate '.($id+1); ?>
                                        </span>
                                    </a>
                                <?php endif ?>
                            </h2>
                            <span class="position" data-sync="<?php echo $old ? 'team_m_position_'.$id : 'team_array_item'.$id.'_position' ?>">
                                <?php echo $teammate['position'] ? $teammate['position'] : 'Teammate Position '.($id+1); ?>
                            </span>
                        </div>
                        <div class="member-bio" data-sync="<?php echo $old ? 'team_m_bio_'.$id : 'team_array_item'.$id.'_bio' ?>">
                            <?php
                                $bio = $teammate['bio'] ? $teammate['bio'] : 'Lorem ipsum dolor sit amet, consec tetur adipisicing elit.';
                                echo apply_filters( 'the_content', $bio );
                            ?>
                        </div>
                        <ul class="user-socials">
                            <?php foreach ($this->get_valid_social_sites() as $social => $name):
                                $link = $teammate[$name] ? $teammate[$name] : false;
                                if( !$link ){continue;}
                                switch ($name) {
                                    case 'google':
                                        $class = "google-plus";
                                        break;
                                    default:
                                         $class = $name;
                                        break;
                                }
                            ?>
                                <li data-toggle="tooltip" title="<?php echo ucfirst($name) ?>"><a href="<?php echo $link ?>"><span class="<?php echo $name ?>"><i class="icon-<?php echo $class ?>"></i></span></a></li>
                            <?php endforeach ?>
                        </ul>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        ob_end_flush();
    }

    function draw_circles($teammate, $main_class, $id){
        $dummy = ( $main_class == 'circle') ? 'http://dummyimage.com/180x180/4d494d/686a82.gif&text=180+x+180' : 'http://dummyimage.com/250/4d494d/686a82.gif&text=250+x+250';

        $image = $teammate['image'] ? $teammate['image'] : $dummy;
        $old = (PL_CORE_VERSION > '1.0.4') ? false : true;

        ob_start();
    ?>
        <div class="<?php echo $main_class ?>">
            <div class="team-item inner-<?php echo $main_class ?>">
                <div class="member-wrapper clear">
                    <ul class="user-socials">
                        <?php foreach ($this->get_valid_social_sites() as $social => $name):
                            $link = $teammate[$name] ? $teammate[$name] : false;
                            if( !$link ){continue;}
                            switch ($name) {
                                case 'google':
                                    $class = "google-plus";
                                    break;
                                default:
                                     $class = $name;
                                    break;
                            }
                        ?>
                            <li data-toggle="tooltip" title="<?php echo ucfirst($name) ?>"><a href="<?php echo $link ?>"><span class="<?php echo $name ?>"><i class="icon-<?php echo $class ?>"></i></span></a></li>
                        <?php endforeach ?>
                    </ul>
                    <div class="member-avatar <?php echo $teammate['external'] ? 'link' : '' ;?>">
                        <?php if (!$teammate['external']): ?>
                            <img data-sync="<?php echo $old ? 'team_m_image_'.$id : 'team_array_item'.$id.'_image' ?>" src="<?php echo $image ?>" alt="<?php echo $teammate['name'] ?>">
                        <?php else: ?>
                            <a href="<?php echo $teammate['external'] ?>">
                                <img data-sync="<?php echo $old ? 'team_m_image_'.$id : 'team_array_item'.$id.'_image' ?>" src="<?php echo $image ?>" alt="<?php echo $teammate['name'] ?>">
                            </a>
                        <?php endif ?>
                    </div>
                </div>
                <div class="member-title">
                    <h2>
                        <?php if (!$teammate['external']): ?>
                            <span data-sync="<?php echo $old ? 'team_m_name_'.$id : 'team_array_item'.$id.'_name' ?>">
                                <?php echo $teammate['name'] ? $teammate['name'] : 'Teammate '.($id+1); ?>
                            </span>
                        <?php else: ?>
                             <a href="<?php echo $teammate['external'] ?>">
                                <span data-sync="<?php echo $old ? 'team_m_name_'.$id : 'team_array_item'.$id.'_name' ?>">
                                    <?php echo $teammate['name'] ? $teammate['name'] : 'Teammate '.($id+1); ?>
                                </span>
                            </a>
                        <?php endif ?>
                    </h2>
                    <span class="position" data-sync="<?php echo $old ? 'team_m_position_'.$id : 'team_array_item'.$id.'_position' ?>">
                        <?php echo $teammate['position'] ? $teammate['position'] : 'Teammate Position '.($id+1); ?>
                    </span>
                </div>
                <div class="member-bio" data-sync="<?php echo $old ? 'team_m_bio_'.$id : 'team_array_item'.$id.'_bio' ?>">
                    <?php
                        $bio = $teammate['bio'] ? $teammate['bio'] : 'Lorem ipsum dolor sit amet, consec tetur adipisicing elit.';
                        echo apply_filters( 'the_content', $bio );
                    ?>
                </div>
            </div>
        </div>
    <?php
        ob_end_flush();
    }

	function section_opts(){
        $help = '
            <h4>Please Flush cache</h4>
            <div>In order to load the LESS/CSS files correctly after you install the section, please, Go to "Global Options" -> "Resets" and click the "Flush Caches" Button.<br><br>If you miss this step, the section will shows unstyled, you will need to do this only one time for each layout.</div>
        ';

        $options = array();

        $options[] = array(
            'key' => 'team-help-setup',
            'title' => 'Flush LESS/CSS cache',
            'type' => 'template',
            'template' => $help,
            'col' => 1
        );

        $options[] = array(
            'type' => 'multi',
            'title' => __('Teammate Configuration', 'tmteammate'),
            'label' => __('Teammate Configuration', 'tmteammate'),
            'opts' => array(
                array(
                    'key'          => "team_boxes",
                    'type'         => 'count_select',
                    'count_start'  => 1,
                    'count_number' => 4,
                    'label'        => __('Number of team boxes to configure', 'tmteammate')
                ),
                array(
                    'key'          => 'team_span',
                    'type'         => 'count_select',
                    'count_start'  => 1,
                    'count_number' => 12,
                    'label'        => __('Number of Columns for each box (12 Col Grid)', 'tmteammate')
                ),
                array(
                    'key'       => 'team_bg_img',
                    'type'      => 'color',
                    'title'     => __('Background Color','tmteammate'),
                    'default'   => '#fafafa'
                ),
                array(
                    'key'       => 'team_bg_img_border',
                    'type'      => 'color',
                    'title'     => __('Border Color','tmteammate'),
                    'default'   => '#eae8e8'
                ),
                array(
                    'key'   => 'team_layout',
                    'type'  => 'select',
                    'title' => __('Teammate Layout', 'tmteammate'),
                    'label' => __('Layout', 'tmteammate'),
                    'opts'  => array(
                        'circle' => array('name' => __('Circle - Default', 'tmteammate')),
                        'square' => array('name' => __('Square', 'tmteammate')),
                        'card'   => array('name' => __('Card', 'tmteammate'))


                    )
                )
            )

        );

        if( PL_CORE_VERSION > '1.0.4' ){
            unset( $options[1]['opts'][0] );
            $options = $this->create_accordion($options);
        }else{
            $options = $this->create_box_settings($options);
        }


        return $options;
    }

    function create_accordion($options){

        $box = array(
            'key' => 'team_array',
            'type' => 'accordion',
            'col' => 2,
            'title' => __( 'Team Member Settings', 'tmteammate' ),
            'post_type' => 'Go the hell',
            'opts'  => array(
                    array(
                        'key'   => 'name',
                        'type'  => 'text',
                        'label' => __('Teammate Name', 'tmteammate'),
                    ),
                    array(
                        'key'   => 'position',
                        'type'  => 'text',
                        'label' => __('Teammate Position', 'tmteammate'),
                    ),
                    array(
                        'key'   => 'image',
                        'type'  => 'image_upload',
                        'title' => __('Teammate image','tmteammate'),
                        'help'  => __('The image size must be 1:1 min size 180x180', 'tmteammate')
                    ),
                    array(
                        'key'   => 'external',
                        'type'  => 'text',
                        'title' => __('Teammate extenal URL','tmteammate')
                    ),
                    array(
                        'key'   => 'bio',
                        'type'  => 'textarea',
                        'title' => __('Teammate short bio.', 'tmteammate')
                    )
                )
        );

            $socials = $this->get_social_fields_accordion();

            foreach ($socials as $social) {
                array_push($box['opts'], $social);
            }

            array_push($options, $box);

        return $options;
    }

    function create_box_settings($opts){
        $loopCount = (  $this->opt('team_boxes') ) ? $this->opt('team_boxes') : 0;
        for ($i=0; $i < $loopCount; $i++) {
            $box = array(
                'key'   => 'team_box_'.$i,
                'type'  =>  'multi',
                'title' => 'Team Member ' . ($i+1) .' Settings',
                'label' => 'Settings',
                'opts'  => array(
                    array(
                        'key'   => 'team_m_name_' .$i,
                        'type'  => 'text',
                        'label' => __('Teammate Name', 'tmteammate'),
                    ),
                    array(
                        'key'   => 'team_m_position_' .$i,
                        'type'  => 'text',
                        'label' => __('Teammate Position', 'tmteammate'),
                    ),
                    array(
                        'key'   => 'team_m_image_' .$i,
                        'type'  => 'image_upload',
                        'title' => __('Teammate image','tmteammate'),
                        'help'  => __('The image size must be 1:1 min size 180x180', 'tmteammate')
                    ),
                    array(
                        'key'   => 'team_m_external_'.$i,
                        'type'  => 'text',
                        'title' => __('Teammate extenal URL','tmteammate')
                    ),
                    array(
                        'key'   => 'team_m_bio_'.$i,
                        'type'  => 'textarea',
                        'title' => __('Teammate short bio.', 'teammate')
                    )
                )
            );

            $socials = $this->get_social_fields($i);

            foreach ($socials as $social) {
                array_push($box['opts'], $social);
            }

            array_push($opts, $box);

        }
        return $opts;
    }

    function get_social_fields($id)
    {
        $out = array();
        foreach ($this->get_valid_social_sites() as $social => $name)
        {
            $tmp = array(
                'key'   => $name . '_url_'.$id,
                'label' => ucfirst($name),
                'type'  => 'text'
            );
            array_push($out, $tmp);
        }
        return $out;
    }

    function get_social_fields_accordion()
    {
        $out = array();
        foreach ($this->get_valid_social_sites() as $social => $name)
        {
            $tmp = array(
                'key'   => $name,
                'label' => ucfirst($name),
                'type'  => 'text'
            );
            array_push($out, $tmp);
        }
        return $out;
    }

    function get_valid_social_sites()
    {
        return array("dribbble", "facebook", "github", "google", "linkedin" ,"pinterest", "tumblr", "twitter");
    }
    // Custom Upgrate my count start in 0 not in 1.
    function upgrade_to_array_format_from_zero( $new_key, $array, $mapping, $number ){
        $scopes = array('local', 'type', 'global');
        if( ! $number )
        {
            return $array;
        }

        if( !$array || $array == 'false' || empty( $array ) )
        {
            for($i = 0; $i < $number; $i++)
            {
                // Set up new output for viewing
                foreach( $mapping as $new_index_key => $old_option_key ){
                    $old_settings[ $i ][ $new_index_key ] = $this->opt( sprintf($old_option_key, $i) );
                }

                // Load up old values using cascade
                foreach( $scopes as $scope )
                {
                    foreach( $mapping as $new_index_key => $old_option_key ){
                        $upgrade_array[$scope]['item'.($i+1)][ $new_index_key ] = $this->opt( sprintf($old_option_key, $i), array('scope' => $scope) );
                    }
                }
            }
            // Setup in new format & update
            foreach($scopes as $scope)
            {
                $this->opt_update( $new_key, $upgrade_array[$scope], $scope );
            }
            return $old_settings;


        } else{
            return $array;
        }
    }
}