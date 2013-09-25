<?php
/*
	Section: Teammate
	Author: Enriue Chavez
	Author URI: http://enriquechavez.co
	Description: Teammate is a DMS section that allows you to show details for a company member or work team member. Every teammate box has up to 12 configuration options: Avatar, Name, Position, mini-bio, and up to 8 social media links. This section can be used to create a detailed "About Us", "Meet the team", or can even be used to create a "Testimonials" page.
	Class Name: TMTeammate
	Demo: http://dms.tmeister.net/teammate
	Version: 1.0
	Filter: misc
*/

class TMTeammate extends PageLinesSection {

    var $section_name      = 'Teammate';
    var $section_version   = '1.0';
    var $section_key ;
    var $chavezShop;

    function section_persistent()
    {
        $this->section_key = strtolower( str_replace(' ', '_', $this->section_name) );
        $this->verify_license();
        add_filter('pl_sorted_settings_array', array(&$this, 'add_global_panel'));
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

        if( $boxes == false){
            echo setup_section_notify($this, __('Please start adding some teammates.', 'tmteammate'));
        }
    ?>
        <div class="row tab<?php echo $this->meta['clone'] ?>">
            <?php for ($i=0; $i<$boxes; $i++): ?>
                <div class="span<?php echo $this->opt('team_span') ?>">
                    <?php
                        switch ($layout) {
                            case 'square':
                                $this->draw_circles($i, 'square');
                                break;
                            case 'card':
                                $this->draw_cards($i, 'card');
                                break;
                            case 'circle':
                            default:
                                $this->draw_circles($i, 'circle');
                        }
                    ?>
                </div>
            <?php endfor ?>
        </div>
    <?php
    }

    function draw_cards($id, $main_class){
        $image = $this->opt('team_m_image_'.$id) ? $this->opt('team_m_image_'.$id) : "http://dummyimage.com/100/4d494d/686a82.gif&text=100+x+100";
        ob_start();
    ?>
        <div class="<?php echo $main_class ?>">
            <div class="team-item inner-<?php echo $main_class ?>">
                <div class="member-wrapper clear">
                    <div class="team-avatar">
                        <img data-sync="team_m_image_<?php echo $id ?>" src="<?php echo $image ?>" alt="<?php echo $this->opt('team_m_name_'.$id) ?>">
                    </div>
                    <div class="team-content">
                        <div class="member-title">
                            <h2>
                                <?php if (!$this->opt('team_m_external_'.$id)): ?>
                                    <span data-sync="team_m_name_<?php echo $id ?>">
                                        <?php echo $this->opt('team_m_name_'.$id) ? $this->opt('team_m_name_'.$id) : 'Teammate '.($id+1); ?>
                                    </span>
                                <?php else: ?>
                                     <a href="<?php echo $this->opt('team_m_external_'.$id) ?>">
                                        <span data-sync="team_m_name_<?php echo $id ?>">
                                            <?php echo $this->opt('team_m_name_'.$id) ? $this->opt('team_m_name_'.$id) : 'Teammate '.($id+1); ?>
                                        </span>
                                    </a>
                                <?php endif ?>
                            </h2>
                            <span class="position" data-sync="team_m_position_<?php echo $id ?>">
                                <?php echo $this->opt('team_m_position_'.$id) ? $this->opt('team_m_position_'.$id) : 'Teammate Position '.($id+1); ?>
                            </span>
                        </div>
                        <div class="member-bio" data-sync="team_m_bio_<?php echo $id ?>">
                            <?php
                                $bio = $this->opt('team_m_bio_'.$id) ? $this->opt('team_m_bio_'.$id) : 'Lorem ipsum dolor sit amet, consec tetur adipisicing elit.';
                                echo apply_filters( 'the_content', $bio );
                            ?>
                        </div>
                        <ul class="user-socials">
                            <?php foreach ($this->get_valid_social_sites() as $social => $name):
                                $link = $this->opt($name . '_url_'.$id) ? $this->opt($name . '_url_'.$id) : false;
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

    function draw_circles($id, $main_class){
        $dummy = ( $main_class == 'circle') ? 'http://dummyimage.com/180x180/4d494d/686a82.gif&text=180+x+180' : 'http://dummyimage.com/250/4d494d/686a82.gif&text=250+x+250';
        $image = $this->opt('team_m_image_'.$id) ? $this->opt('team_m_image_'.$id) : $dummy;
        ob_start();
    ?>
        <div class="<?php echo $main_class ?>">
            <div class="team-item inner-<?php echo $main_class ?>">
                <div class="member-wrapper clear">
                    <ul class="user-socials">
                        <?php foreach ($this->get_valid_social_sites() as $social => $name):
                            $link = $this->opt($name . '_url_'.$id) ? $this->opt($name . '_url_'.$id) : false;
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
                    <div class="member-avatar <?php echo $this->opt('team_m_external_'.$id) ? 'link' : '' ;?>">
                        <?php if (!$this->opt('team_m_external_'.$id)): ?>
                            <img data-sync="team_m_image_<?php echo $id ?>" src="<?php echo $image ?>" alt="<?php echo $this->opt('team_m_name_'.$id) ?>">
                        <?php else: ?>
                            <a href="<?php echo $this->opt('team_m_external_'.$id) ?>">
                                <img data-sync="team_m_image_<?php echo $id ?>" src="<?php echo $image ?>" alt="<?php echo $this->opt('team_m_name_'.$id) ?>">
                            </a>
                        <?php endif ?>
                    </div>
                </div>
                <div class="member-title">
                    <h2>
                        <?php if (!$this->opt('team_m_external_'.$id)): ?>
                            <span data-sync="team_m_name_<?php echo $id ?>">
                                <?php echo $this->opt('team_m_name_'.$id) ? $this->opt('team_m_name_'.$id) : 'Teammate '.($id+1); ?>
                            </span>
                        <?php else: ?>
                             <a href="<?php echo $this->opt('team_m_external_'.$id) ?>">
                                <span data-sync="team_m_name_<?php echo $id ?>">
                                    <?php echo $this->opt('team_m_name_'.$id) ? $this->opt('team_m_name_'.$id) : 'Teammate '.($id+1); ?>
                                </span>
                            </a>
                        <?php endif ?>
                    </h2>
                    <span class="position" data-sync="team_m_position_<?php echo $id ?>">
                        <?php echo $this->opt('team_m_position_'.$id) ? $this->opt('team_m_position_'.$id) : 'Teammate Position '.($id+1); ?>
                    </span>
                </div>
                <div class="member-bio" data-sync="team_m_bio_<?php echo $id ?>">
                    <?php
                        $bio = $this->opt('team_m_bio_'.$id) ? $this->opt('team_m_bio_'.$id) : 'Lorem ipsum dolor sit amet, consec tetur adipisicing elit.';
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
        $opts = array(
            array(
                'key' => 'team-help-setup',
                'title' => 'Flush LESS/CSS cache',
                'type' => 'template',
                'template' => $help
            ),
            array(
                'key'   => 'team-setup',
                'type'  => 'multi',
                'title' => __('Teammate Configuration', 'tmteammate'),
                'label' => __('Teammate Configuration', 'tmteammate'),
                'opts'  => array(
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
            )
        );

        $opts = $this->create_box_settings($opts);

        return $opts;
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
                        'title' => __('Teammate short bio.')
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

    function get_valid_social_sites()
    {
        return array("dribbble", "facebook", "github", "google", "linkedin" ,"pinterest", "tumblr", "twitter");
    }


}