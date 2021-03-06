<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS_Controller class
 *
 * @author gofrendi
 */

class CMS_Controller extends MX_Controller
{
    public $PRIV_EVERYONE           = 1;
    public $PRIV_NOT_AUTHENTICATED  = 2;
    public $PRIV_AUTHENTICATED      = 3;
    public $PRIV_AUTHORIZED         = 4;

    private $__cms_widgets          = NULL;
    private $__cms_navigations      = NULL;
    private $__cms_navigation_path  = NULL;
    private $__cms_navigation_name  = NULL;
    private $__cms_quicklinks       = NULL;

    protected $REFERRER = NULL;

    public function __construct()
    {
        parent::__construct();
        /* Standard Libraries */
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('cms_helper');
        $this->load->library('form_validation');

        // if there is old_url, then save it
        $this->load->library('session');
        $old_url = $this->session->flashdata('cms_old_url');
        if (!is_bool($old_url)) {
            $this->session->keep_flashdata('cms_old_url');
        }
        /* ------------------ */

        $this->load->library('grocery_CRUD');
        $this->load->library('template');

        $this->load->model('No_CMS_Model');
        // just for autocompletion, never run
        if(false) $this->No_CMS_Model = new No_CMS_Model();
    }

    /**
     * @author goFrendiAsgard
     * @param  string $table_name
     * @return string
     * @desc   return complete table name
     */
    public function cms_complete_table_name($table_name){
        return $this->No_CMS_Model->cms_complete_table_name($table_name);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $navigation_name
     * @return string
     * @desc   return complete navigation name
     */
    public function cms_complete_navigation_name($navigation_name){
        return $this->No_CMS_Model->cms_complete_navigation_name($navigation_name);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     * @desc   if value specified, this will set CI_Session["key"], else it will return CI_session["key"]
     */
    public function cms_ci_session($key, $value = NULL)
    {
        return $this->No_CMS_Model->cms_ci_session($key, $value);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $key
     * @desc   unset CI_session["key"]
     */
    public function cms_unset_ci_session($key)
    {
        return $this->No_CMS_Model->cms_unset_ci_session($key);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $user_name
     * @return mixed
     * @desc   set or get CI_Session["cms_user_name"]
     */
    protected function cms_user_name($user_name = NULL)
    {
        return $this->No_CMS_Model->cms_user_name($user_name);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $real_name
     * @return mixed
     * @desc   set or get CI_Session["cms_user_real_name"]
     */
    protected function cms_user_real_name($real_name = NULL)
    {
        return $this->No_CMS_Model->cms_user_real_name($real_name);
    }

    /**
     * @author goFrendiAsgard
     * @param  string $email
     * @return mixed
     * @desc   set or get CI_Session["cms_user_email"]
     */
    protected function cms_user_email($email = NULL)
    {
        return $this->No_CMS_Model->cms_user_email($email);
    }

    /**
     * @author goFrendiAsgard
     * @param  int $user_id
     * @desc   set or get CI_Session["cms_user_id"]
     */
    protected function cms_user_id($user_id = NULL)
    {
        return $this->No_CMS_Model->cms_user_id($user_id);
    }

    /**
     * @author  goFrendiAsgard
     * @param   int parent_id
     * @param   int max_menu_depth
     * @desc    return navigation child if parent_id specified, else it will return root navigation
     *           the max depth of menu is depended on max_menud_depth
     */
    public function cms_navigations($parent_id = NULL, $max_menu_depth = NULL)
    {
        return $this->No_CMS_Model->cms_navigations($parent_id, $max_menu_depth);
    }

    /**
     * @author goFrendiAsgard
     * @return mixed
     * @desc   return quick links
     */
    public function cms_quicklinks()
    {
        return $this->No_CMS_Model->cms_quicklinks();
    }

    /**
     * @author  goFrendiAsgard
     * @param   slug
     * @param   widget_name
     * @return  mixed
     * @desc    return widgets
     */
    public function cms_widgets($slug = NULL, $widget_name = NULL)
    {
        return $this->No_CMS_Model->cms_widgets($slug, $widget_name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string navigation_name
     * @return  string
     * @desc    return submenu screen
     */
    public function cms_submenu_screen($navigation_name)
    {
        return $this->No_CMS_Model->cms_submenu_screen($navigation_name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string navigation_name
     * @return  mixed
     * @desc    return navigation path, used for layout
     */
    public function cms_get_navigation_path($navigation_name = NULL)
    {
        return $this->No_CMS_Model->cms_get_navigation_path($navigation_name);
    }

    /**
     * @author  goFrendiAsgard
     * @return  mixed
     * @desc    return privileges of current user
     */
    public function cms_privileges()
    {
        return $this->No_CMS_Model->cms_privileges();
    }

    /**
     * @author  goFrendiAsgard
     * @param   string navigation_name
     * @return  bool
     * @desc    check if user authorized to navigate into a page specified in parameter
     */
    protected function cms_allow_navigate($navigation_name)
    {
        return $this->No_CMS_Model->cms_allow_navigate($navigation_name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string privilege_name
     * @return  bool
     * @desc    check if user have privilege specified in parameter
     */
    protected function cms_have_privilege($privilege_name)
    {
        return $this->No_CMS_Model->cms_have_privilege($privilege_name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string identity
     * @param   string password
     * @return  bool
     * @desc    login with identity and password. Identity can be user_name or e-mail
     */
    protected function cms_do_login($identity, $password)
    {
        return $this->No_CMS_Model->cms_do_login($identity, $password);
    }

    /**
     * @author  goFrendiAsgard
     * @desc    logout
     */
    protected function cms_do_logout()
    {
        $this->No_CMS_Model->cms_do_logout();
    }

    /**
     * @author  goFrendiAsgard
     * @param   string user_name
     * @param   string email
     * @param   string real_name
     * @param   string password
     * @desc    register new user
     */
    protected function cms_do_register($user_name, $email, $real_name, $password)
    {
        return $this->No_CMS_Model->cms_do_register($user_name, $email, $real_name, $password);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string user_name
     * @param   string email
     * @param   string real_name
     * @param   string password
     * @desc    change current profile (user_name, email, real_name and password)
     */
    protected function cms_do_change_profile($user_name, $email, $real_name, $password = NULL)
    {
        return $this->No_CMS_Model->cms_do_change_profile($user_name, $email, $real_name, $password);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string module_name
     * @return  bool
     * @desc    checked if module installed
     */
    protected function cms_is_module_active($module_name)
    {
        return $this->No_CMS_Model->cms_is_module_active($module_name);
    }

    /**
     * @author  goFrendiAsgard
     * @return  mixed
     * @desc    get module list
     */
    public function cms_get_module_list()
    {
        return $this->No_CMS_Model->cms_get_module_list();
    }

    /**
     * @author  goFrendiAsgard
     * @param   string module_name
     * @return  string
     * @desc    get module_path (folder name) of specified module_name (name space)
     */
    public function cms_module_path($module_name = NULL)
    {
        return $this->No_CMS_Model->cms_module_path($module_name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string module_path
     * @return  string
     * @desc    get module_name (name space) of specified module_path (folder name)
     */
    public function cms_module_name($path)
    {
        return $this->No_CMS_Model->cms_module_name($path);
    }

    /**
     * @author  goFrendiAsgard
     * @return  mixed
     * @desc    get layout list
     */
    protected function cms_get_layout_list()
    {
        return $this->No_CMS_Model->cms_get_layout_list();
    }

    /**
     * @author  goFrendiAsgard
     * @param   string identity
     * @param	bool send_mail
     * @param   string reason (FORGOT, SIGNUP)
     * @return  bool
     * @desc    generate activation code, and send email to applicant
     */
    protected function cms_generate_activation_code($identity, $send_mail = FALSE, $reason = 'FORGOT')
    {
        return $this->No_CMS_Model->cms_generate_activation_code($identity, $send_mail, $reason);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string activation_code
     * @param   string new_password
     * @return  bool success
     * @desc    activate user
     */
    protected function cms_activate_account($activation_code, $new_password = NULL)
    {
        return $this->No_CMS_Model->cms_activate_account($activation_code, $new_password);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string from_address
     * @param   string from_name
     * @param   string to_address
     * @param   string subject
     * @param   string message
     * @desc    send email
     */
    protected function cms_send_email($from_address, $from_name, $to_address, $subject, $message)
    {
        return $this->No_CMS_Model->cms_send_email($from_address, $from_name, $to_address, $subject, $message);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string activation_code
     * @return  bool
     * @desc    validate activation_code
     */
    protected function cms_valid_activation_code($activation_code)
    {
        return $this->No_CMS_Model->cms_valid_activation_code($activation_code);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string name
     * @param   string value
     * @param   string description
     * @desc    set config variable
     */
    protected function cms_set_config($name, $value, $description = NULL)
    {
        return $this->No_CMS_Model->cms_set_config($name, $value, $description);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string name
     * @desc    unset configuration variable
     */
    protected function cms_unset_config($name)
    {
        return $this->No_CMS_Model->cms_unset_config($name);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string name, bool raw
     * @return  string
     * @desc    get configuration variable
     */
    public function cms_get_config($name, $raw = False)
    {
        return $this->No_CMS_Model->cms_get_config($name, $raw);
    }

    /**
     * @author	goFrendiAsgard
     * @param	string language
     * @return	string language
     * @desc	set language for this session only
     */
    protected function cms_language($language = NULL)
    {
        return $this->No_CMS_Model->cms_language($language);
    }

    /**
     * @author	goFrendiAsgard
     * @return	array list of available languages
     * @desc	get available languages
     */
    public function cms_language_list()
    {
        return $this->No_CMS_Model->cms_language_list();
    }

    /**
     * @author  goFrendiAsgard
     * @param   string key
     * @return  string
     * @desc    get translation of key in site_language
     */
    protected function cms_lang($key, $module = NULL)
    {
        return $this->No_CMS_Model->cms_lang($key, $module);
    }

    /**
     * @author goFrendiAsgard
     * @param  string value
     * @return string
     * @desc   parse keyword like @site_url and @base_url
     */
    public function cms_parse_keyword($value)
    {
        return $this->No_CMS_Model->cms_parse_keyword($value);
    }

    /**
     * @author goFrendiAsgard
     * @param  string user_name
     * @return bool
     * @desc   check if user already exists
     */
    public function cms_is_user_exists($username)
    {
        return $this->No_CMS_Model->cms_is_user_exists($username);
    }

    /**
     * @author goFrendiAsgard
     * @param  string url_string
     * @return bool
     * @desc   guess the navigation name of an url
     */
    protected function cms_navigation_name($url_string = NULL)
    {
        if (!isset($url_string)) {
            $url_string = $this->uri->ruri_string();
            if(strlen($url_string)>0 && $url_string[0]){
                $url_string = substr($url_string, 1);
            }
        }
        $SQL             = "SELECT navigation_name
        	FROM ".cms_table_name('main_navigation')."
        	WHERE '" . addslashes($url_string) . "' LIKE CONCAT(url,'%')
        		OR '/" . addslashes($url_string) . "/' LIKE CONCAT(url,'%')
        		OR '/" . addslashes($url_string) . "' LIKE CONCAT(url,'%')
        		OR '" . addslashes($url_string) . "/' LIKE CONCAT(url,'%')
        	ORDER BY LENGTH(url) DESC";
        $query           = $this->db->query($SQL);
        $navigation_name = NULL;
        if ($query->num_rows() > 0) {
            $row             = $query->row();
            $navigation_name = stripslashes($row->navigation_name);
        }
        return $navigation_name;
    }

    /**
     * @author goFrendiAsgard
     * @desc   redirect to login/main page
     */
    protected function cms_redirect()
    {
        $uriString = $this->uri->uri_string();
        $old_url   = $this->session->flashdata('old_url');
        if (is_bool($old_url)) {
            $this->session->set_flashdata('cms_old_url', $uriString);
        }

        if ($this->cms_allow_navigate('main_login')) {
            redirect('main/login');
        } else {
            $navigation_name = $this->cms_navigation_name($this->router->routes['default_controller']);
            if (!isset($navigation_name)) {
                $navigation_name = $this->cms_navigation_name($this->router->routes['default_controller'] . '/index');
            }
            // redirect to default controller
            if (isset($navigation_name) && $this->cms_allow_navigate($navigation_name)) {
                redirect('');
            } else {
                show_404();
            }
        }
    }

    /**
     * @author goFrendiAsgard
     * @param string navigation_name
     * @param string or array privilege_required
     * @desc guard a page from unauthorized access
     */
    public function cms_guard_page($navigation_name = NULL, $privilege_required = NULL)
    {
        $privilege_required = isset($privilege_required) ? $privilege_required : array();

        // check if allowed
        if (!isset($navigation_name) || $this->cms_allow_navigate($navigation_name)) {
            if (!isset($privilege_required)) {
                $allowed = true;
            } else if (is_array($privilege_required)) {
                // privilege_required is array
                $allowed = true;
                foreach ($privilege_required as $privilege) {
                    $allowed = $allowed && $this->cms_have_privilege($privilege);
                    if (!$allowed)
                        break;
                }
            } else { // privilege_required is string
                $allowed = $this->cms_have_privilege($privilege_required);
            }
        } else {
            $allowed = false;
        }

        // if not allowed then redirect
        if (!$allowed) {
            $this->cms_redirect();
        }
    }

    /**
     * @author  goFrendiAsgard
     * @param   string view_url
     * @param   string data
     * @param   string navigation_name
     * @param   array config
     * @param   bool return_as_string
     * @return  string or null
     * @desc    replace $this->load->view. This method will also load header, menu etc except there is _only_content parameter via GET or POST
     */
    protected function view($view_url, $data = NULL, $navigation_name = NULL, $config = NULL, $return_as_string = FALSE)
    {
        $result   = NULL;
        $view_url = $this->cms_parse_keyword($view_url);

        /**
         * PREPARE PARAMETERS *********************************************************************************************
         */

        // this method can be called as $this->view('view_path', $data, true);
        // or $this->view('view_path', $data, $navigation_name, true);
        if (is_bool($navigation_name) && count($config) == 0) {
            $return_as_string = $navigation_name;
            $navigation_name  = NULL;
            $config           = NULL;
        } else if (is_bool($config)) {
            $return_as_string = $config;
            $config           = NULL;
        }

        if (!isset($return_as_string))
            $return_as_string = FALSE;
        if (!isset($config))
            $config = array();

        $privilege_required = isset($config['privileges']) ? $config['privileges'] : array();
        $custom_theme       = isset($config['theme']) ? $config['theme'] : NULL;
        $custom_layout      = isset($config['layout']) ? $config['layout'] : NULL;
        $custom_title       = isset($config['title']) ? $config['title'] : NULL;
        $custom_metadata    = isset($config['metadata']) ? $config['metadata'] : array();
        $custom_partial     = isset($config['partials']) ? $config['partials'] : NULL;
        $custom_keyword     = isset($config['keyword']) ? $config['keyword'] : NULL;
        $only_content       = isset($config['only_content']) ? $config['only_content'] : NULL;
        $always_allow       = isset($config['always_allow']) ? $config['always_allow'] : FALSE;
        $layout_suffix      = isset($config['layout_suffix']) ? $config['layout_suffix'] : '';

        /**
         * CHECK IF IT IS WIDGET
         */
        $dynamic_widget = $this->cms_ci_session('cms_dynamic_widget');
        $this->cms_unset_ci_session('cms_dynamic_widget');

        /**
         * GUESS $navigation_name THROUGH ITS URL  ***********************************************************************
         */
        if (!$dynamic_widget && !isset($navigation_name)) {
            $navigation_name = $this->cms_navigation_name();
        }

        /**
         * CHECK IF THE CURRENT NAVIGATION IS ACCESSIBLE  *****************************************************************
         */
        if (!$always_allow) {
            $this->cms_guard_page($navigation_name, $privilege_required);
        }

        /**
         * CHECK IF THE PAGE IS STATIC  **********************************************************************************
         */
        $data = (array) $data;
        if (isset($navigation_name) && !isset($data['_content'])) {
            $SQL   = "SELECT static_content FROM ".cms_table_name('main_navigation').
                " WHERE is_static=1 AND navigation_name='".addslashes($navigation_name)."'
                  AND (ISNULL(url) OR navigation_name='".addslashes($this->cms_navigation_name())."')";
            $query = $this->db->query($SQL);
            if ($query->num_rows() > 0) {
                $row            = $query->row();
                $static_content = $row->static_content;
                // static_content should contains string
                if (!$static_content) {
                    $static_content = '';
                }
                $data['cms_content'] = $static_content;
                $view_url            = 'CMS_View';

            }
        }


        /**
         * SHOW THE PAGE IF IT IS ACCESSIBLE  *****************************************************************************
         */


        // GET THE THEME, TITLE & ONLY_CONTENT FROM DATABASE
        $theme         = '';
        $title         = '';
        $keyword       = '';
        $default_theme = NULL;
        $page_title    = NULL;
        $page_keyword  = NULL;
        if (isset($navigation_name)) {
            $SQL   = "SELECT title, page_title, page_keyword, default_theme, only_content FROM ".cms_table_name('main_navigation')." WHERE navigation_name = '" . addslashes($navigation_name) . "'";
            $query = $this->db->query($SQL);
            // get default_theme, and default_title of this page
            if ($query->num_rows() > 0) {
                $row           = $query->row();
                $default_theme = $row->default_theme;
                if (isset($row->page_title) && $row->page_title != '') {
                    $page_title = $row->page_title;
                } else if (isset($row->title) && $row->title != '') {
                    $page_title = $row->title;
                }
                $page_keyword = isset($row->page_keyword) ? $row->page_keyword : '';
                if (!isset($only_content)) {
                    $only_content = ($row->only_content == 1);
                }
            }
        }
        if (!isset($only_content)) {
            $only_content = TRUE;
        }

        // ASSIGN THEME
        if (isset($custom_theme)) {
            $theme = $custom_theme;
        } else if (isset($default_theme) && $default_theme != '') {
            $themes     = $this->cms_get_layout_list();
            $theme_path = array();
            foreach ($themes as $theme) {
                $theme_path[] = $theme['path'];
            }
            if (in_array($default_theme, $theme_path)) {
                $theme = $default_theme;
            }
        } else {
            $theme = $this->cms_get_config('site_theme');
        }

        // ASSIGN TITLE
        $title = '';
        if (isset($custom_title)) {
            $title = $custom_title;
        } else if (isset($page_title) && $page_title != '') {
            $title = $page_title;
        } else {
            $title = $this->cms_get_config('site_name');
        }

        // ASSIGN KEYWORD
        if (isset($page_keyword) && $page_keyword != '') {
            $keyword = $page_keyword;
            if ($custom_keyword != '') {
                $keyword .= ', ' . $custom_keyword;
            }
        } else {
            $keyword = $custom_keyword;
        }


        // GET THE LAYOUT
        if (isset($custom_layout)) {
            $layout = $custom_layout;
        } else {
            $this->load->library('user_agent');
            $layout = $this->agent->is_mobile() ? 'mobile' : 'default';
        }


        // ADJUST THEME AND LAYOUT
        if (!$this->cms_layout_exists($theme, $layout)) {
            if ($layout == 'mobile' && $this->cms_layout_exists($theme, 'default')) {
                $layout = 'default';
            } else {
                $theme = 'neutral';
            }
        }

        // ADD AUTHENTICATED SUFFIX (in case of user has logged in)
        $cms_user_id = $this->cms_user_id();
        if ($layout_suffix == '' && isset($cms_user_id) && $cms_user_id) {
            $layout_suffix = 'authenticated';
        }

        if ($this->cms_layout_exists($theme, $layout . '_' . $layout_suffix)) {
            $layout = $layout . '_' . $layout_suffix;
        }

        // IT'S SHOW TIME
        if ($only_content || $dynamic_widget || (isset($_REQUEST['_only_content'])) || $this->input->is_ajax_request()) {
            $result = $this->load->view($view_url, $data, TRUE);
            $result = $this->cms_parse_keyword($result);
            if ($return_as_string) {
                return $result;
            } else {
                $this->cms_show_html($result);
            }
        } else {
            // set theme, layout and title
            $this->template->title($title);
            $this->template->set_theme($theme);
            $this->template->set_layout($layout);

            // set keyword metadata
            if ($keyword != '') {
                $keyword_metadata = '<meta name="keyword" content="' . $keyword . '">';
                $this->template->append_metadata($keyword_metadata);
            }

            $jquery_path = base_url('assets/nocms/js/jquery.tools.min.js');
            $this->template->append_metadata('<script type="text/javascript" src="' . $jquery_path . '"></script>');

            // google analytic
            $analytic_property_id = $this->cms_get_config('cms_google_analytic_property_id');
            if (trim($analytic_property_id) != '') {
                // create analytic code
                $analytic_code  = '<script type="text/javascript"> ';
                $analytic_code .= 'var _gaq = _gaq || []; ';
                $analytic_code .= '_gaq.push([\'_setAccount\', \'' . $analytic_property_id . '\']); ';
                $analytic_code .= '_gaq.push([\'_trackPageview\']); ';
                $analytic_code .= '(function() { ';
                $analytic_code .= 'var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true; ';
                $analytic_code .= 'ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\'; ';
                $analytic_code .= 'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s); ';
                $analytic_code .= '})(); ';
                $analytic_code .= '</script>';
                // add to the template
                $this->template->append_metadata($analytic_code);
            }


            // config metadata
            foreach ($custom_metadata as $metadata) {
                $this->template->append_metadata($metadata);
            }


            $this->load->helper('directory');
            $partial_path = BASEPATH . '../themes/' . $theme . '/views/partials/' . $layout . '/';
            if (is_dir($partial_path)) {
                $partials = directory_map($partial_path, 1);
                foreach ($partials as $partial) {
                    // if is directory or is not php, then ignore it
                    if (is_dir($partial))
                        continue;
                    $partial_extension = pathinfo($partial_path . $partial, PATHINFO_EXTENSION);
                    if (strtoupper($partial_extension) != 'PHP')
                        continue;

                    // add partial to template
                    $partial_name = pathinfo($partial_path . $partial, PATHINFO_FILENAME);
                    if (isset($custom_partial[$partial_name])) {
                        $this->template->inject_partial($partial_name, $custom_partial[$partial_name]);
                    } else {
                        $this->template->set_partial($partial_name, 'partials/' . $layout . '/' . $partial, $data);
                    }
                }
            }

            $result = $this->template->build($view_url, $data, TRUE);
            // parse keyword
            $result = $this->cms_parse_keyword($result);

            $result = $this->No_CMS_Model->cms_escape_template($result);

            // parse widget
            $pattern  = '/\{\{ widget:(.*?) \}\}/si';
            // execute regex
            $result   = preg_replace_callback($pattern, array(
                $this,
                '__cms_preg_replace_callback_widget'
            ), $result);

            // parse widget by name
            $pattern  = '/\{\{ widget_name:(.*?) \}\}/si';
            // execute regex
            $result   = preg_replace_callback($pattern, array(
                $this,
                '__cms_preg_replace_callback_widget_by_name'
            ), $result);

            // parse widget by slug
            $pattern  = '/\{\{ widget_slug:(.*?) \}\}/si';
            // execute regex
            $result   = preg_replace_callback($pattern, array(
                $this,
                '__cms_preg_replace_callback_widget_by_slug'
            ), $result);

            // prepare pattern and replacement
            $pattern     = array();
            $replacement = array();

            // theme
            $pattern[]     = "/\{\{ used_theme \}\}/si";
            $replacement[] = $theme;
            $nav_path   = $this->__cms_build_nav_path($navigation_name);
            $pattern[]     = "/\{\{ navigation_path \}\}/si";
            $replacement[] = $nav_path;

            $result = preg_replace($pattern, $replacement, $result);

            $result = $this->No_CMS_Model->cms_unescape_template($result);


            if ($return_as_string) {
                return $result;
            } else {
                $this->cms_show_html($result);
            }
        }
    }

    private function __cms_build_left_nav($navigations = NULL, $first = TRUE){
        if(!isset($navigations)){
            if(!isset($this->__cms_navigations)){
                $navigations = $this->cms_navigations();
                $this->__cms_navigations =$navigations;
            }else{
                $navigations = $this->__cms_navigations;
            }
        }
        if(count($navigations) == 0) return '';

        if($first){
            $style = 'display: block; position: static; border:none; margin:0px; background-color:light-gray;';
        }else{
            $style = 'background-color:light-gray;';
        }
        $result = '<ul  class="dropdown-menu nav nav-pills nav-stacked" style="'.$style.'">';
        foreach($navigations as $navigation){
            if(($navigation['allowed'] && $navigation['active']) || $navigation['have_allowed_children']){
                // make text
                if($navigation['allowed'] && $navigation['active']){
                    $text = '<a class="dropdown-toggle" href="'.$navigation['url'].'">'.$navigation['title'].'</a>';
                }else{
                    $text = $navigation['title'];
                }

                if(count($navigation['child'])>0 && $navigation['have_allowed_children']){
                    $result .= '<li class="dropdown-submenu">'.$text.$this->__cms_build_left_nav($navigation['child'], FALSE).'</li>';
                }else{
                    $result .= '<li>'.$text.'</li>';
                }
            }
        }
        $result .= '</ul>';
        return $result;
    }

    private function __cms_build_top_nav_btn($navigations = NULL, $caption = 'Complete Menu', $first = TRUE){
        if(!isset($navigations)){
            if(!isset($this->__cms_navigations)){
                $navigations = $this->cms_navigations();
                $this->__cms_navigations =$navigations;
            }else{
                $navigations = $this->__cms_navigations;
            }
        }
        if(count($navigations) == 0) return '';

        $result = '';
        $result .= '<ul class="dropdown-menu">';
        foreach($navigations as $navigation){
            if(($navigation['allowed'] && $navigation['active']) || $navigation['have_allowed_children']){
                // make text
                if($navigation['allowed'] && $navigation['active']){
                    $text = '<a href="'.$navigation['url'].'">'.$navigation['title'].'</a>';
                }else{
                    $text = '<a href="#">'.$navigation['title'].'</a>';
                }

                if(count($navigation['child'])>0 && $navigation['have_allowed_children']){
                    $result .= '<li class="dropdown-submenu">'.$text.$this->__cms_build_top_nav_btn($navigation['child'], $caption, FALSE).'</li>';
                }else{
                    $result .= '<li>'.$text.'</li>';
                }
            }
        }
        $result .= '</ul>';
        if($first){
            $result = '<ul class="nav"><li class="dropdown">'.
                '<a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$caption.' <span class="caret"></span></a>'.
                $result.
                '</li></ul>';
        }
        return $result;
    }

    private function __cms_build_quicklink(){
        if(isset($this->__cms_quicklinks)){
            $quicklinks = $this->__cms_quicklinks;
        }else{
            $quicklinks = $this->cms_quicklinks();
        }
        if(count($quicklinks) == 0) return '';
        $html = '<ul class="nav">';
        foreach($quicklinks as $quicklink){
            $html.= '<li>';
            $html.= anchor($quicklink['url'], $quicklink['title']);
            $html.= '</li>';
        }
        $html.= '</ul>';
        return $html;
    }

    private function __cms_build_widget($slug=NULL, $widget_name=NULL){
        $widgets  = $this->cms_widgets($slug, $widget_name);
        $html = '';
        if(isset($widget_name)){
            foreach($widgets as $slug_widgets){
                if(count($slug_widgets)>0){
                    $widget = $slug_widgets[0];
                    $html = $widget['content'];
                    break;
                }
            }
        }else if(isset($slug) && isset($widgets[$slug])){
            $html = '<div class="cms-widget-slug-'.$slug.'">';
            foreach($widgets[$slug] as $widget){
                $html.= '<div class="cms-widget-container">';
                $html.= '<h5>'.$widget['title'].'</h5>';
                $html.= '<div class="cms-widget-content">'.$widget['content'].'</div>';
                $html.= '<br />';
                $html.= '<br />';
                $html.= '</div>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    private function __cms_build_nav_path($navigation_name){
        $path = $this->cms_get_navigation_path($navigation_name);
        $html = "";
        for($i=0; $i<count($path); $i++){
            $current_path = $path[$i];
            $html .= anchor($current_path['url'], $current_path['title']);
            if($i<count($path)-1){
                $html .= " >> ";
            }
        }
        return $html;
    }

    private function __cms_preg_replace_callback_widget($arr){
        $html = "";
        if(count($arr)>1){
            $slug = $arr[1];
            $html = $this->__cms_build_widget($slug);
        }
        return $html;
    }

    private function __cms_preg_replace_callback_widget_by_name($arr){
        $html = "";
        if(count($arr)>1){
            $widget_name = $arr[1];
            $html = $this->__cms_build_widget(NULL, $widget_name);
        }
        return $html;
    }

    private function __cms_preg_replace_callback_widget_by_slug($arr){
        $html = "";
        if(count($arr)>1){
            $slug = $arr[1];
            $html = $this->__cms_build_widget($slug, NULL);
        }
        return $html;
    }

    public function cms_layout_exists($theme, $layout)
    {
        return is_file('themes/' . $theme . '/views/layouts/' . $layout . '.php');
    }

    private function __cms_cache($time = 5)
    {
        // cache
        $this->load->driver('cache');
        $this->output->cache($time);
    }



    /**
     * @author  goFrendiAsgard
     * @param   mixed variable
     * @param   int options
     * @desc    show variable in json encoded form
     */
    protected function cms_show_json($variable, $options = 0)
    {
        $result = '';
        // php 5.3.0 accepts 2 parameters, while lower version only accepts 1 parameter
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $result = json_encode($variable, $options);
        } else {
            $result = json_encode($variable);
        }
        // show the json
        $this->output->set_content_type('application/json')->set_output($result);
    }

    /**
     * @author  goFrendiAsgard
     * @param   mixed variable
     * @desc    show variable for debugging purpose
     */
    protected function cms_show_variable($variable)
    {
        $data = array(
            'cms_content' => '<pre>' . print_r($variable, TRUE) . '</pre>'
        );
        $this->load->view('CMS_View', $data);
    }

    /**
     * @author  goFrendiAsgard
     * @param   string html
     * @desc    you are encouraged to use this instead of echo $html
     */
    protected function cms_show_html($html)
    {
        $data = array(
            'cms_content' => $html
        );
        $this->load->view('CMS_View', $data);
    }

    /**
     * @author goFrendiAsgard
     * @return array providers
     */
    public function cms_third_party_providers()
    {
        return $this->No_CMS_Model->cms_third_party_providers();
    }

    /**
     * @author goFrendiAsgard
     * @return array status
     * @desc return all status from third-party provider
     */
    public function cms_third_party_status()
    {
        return $this->No_CMS_Model->cms_third_party_status();
    }

    /**
     * @author goFrendiAsgard
     * @return boolean success
     * @desc login/register by using third-party provider
     */
    public function cms_third_party_login($provider)
    {
        return $this->No_CMS_Model->cms_third_party_login($provider);
    }

}


abstract class CMS_Priv_Base_Controller extends CMS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function cms_override_config($config)
    {
        return $config;
    }

    protected function cms_override_navigation_name($navigation_name)
    {
        return $navigation_name;
    }

    protected function view($view_url, $data = NULL, $navigation_name = NULL, $config = NULL, $return_as_string = FALSE)
    {
        if (is_bool($navigation_name) && count($config) == 0) {
            $return_as_string = $navigation_name;
            $navigation_name  = NULL;
            $config           = NULL;
        } else if (is_bool($config)) {
            $return_as_string = $config;
            $config           = NULL;
        }
        if (!isset($config) || !is_array($config)) {
            $config = array();
        }
        $navigation_name = $this->cms_override_navigation_name($navigation_name);
        $config          = $this->cms_override_config($config);
        parent::view($view_url, $data, $navigation_name, $config, $return_as_string);
    }
}

class CMS_Priv_Strict_Controller extends CMS_Priv_Base_Controller
{
    private $navigation_name = '';

    protected $URL_MAP = array();
    protected $ALLOW_UNKNOWN_NAVIGATION_NAME = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->URL_MAP = $this->do_override_url_map($this->URL_MAP);
        $uriString = $this->uri->uri_string();
        $navigation_name = NULL;
        if (isset($this->URL_MAP[$uriString])) {
            if (!isset($navigation_name)) {
                $navigation_name = $this->cms_navigation_name($this->URL_MAP[$uriString]);
            }
            if (!isset($navigation_name)) {
                $navigation_name = $this->URL_MAP[$uriString];
            }
        } else {
            foreach ($this->URL_MAP as $key=>$value) {
                if($uriString == $this->cms_parse_keyword($key)){
                    if (!isset($navigation_name)) {
                        $navigation_name = $this->cms_navigation_name($key);
                    }
                    if (!isset($navigation_name)) {
                        $navigation_name = $this->URL_MAP[$key];
                    }
                    break;
                }
            }
        }
        if (!isset($navigation_name)) {
            $navigation_name = $this->cms_navigation_name($uriString);
        }
        $this->cms_guard_page($navigation_name);
        if (!$this->ALLOW_UNKNOWN_NAVIGATION_NAME && !isset($navigation_name)) {
            if ($this->input->is_ajax_request()) {
                $response = array(
                    'success' => FALSE,
                    'message' => 'unauthorized access'
                );
                $this->cms_show_json($variable);
                die();
            } else {
                $this->cms_redirect();
            }
        }
        $this->navigation_name = $navigation_name;
    }

    protected function do_override_url_map($URL_MAP){
        return $URL_MAP;
    }

    protected function cms_override_navigation_name($navigation_name)
    {
        if (!isset($navigation_name) || $navigation_name == '') {
            $navigation_name = $this->navigation_name;
        }
        return $navigation_name;
    }

    protected function cms_override_config($config)
    {
        $config['always_allow'] = TRUE;
        return $config;
    }
}

/**
 * Description of CMS_Module_Installer
 *
 * @author gofrendi
 */
class CMS_Module_Installer extends CMS_Controller
{
    protected $DEPENDENCIES  = array();
    protected $NAME          = '';
    protected $VERSION       = '0.0.0';
    protected $DESCRIPTION   = 'Just another module ...';
    protected $IS_ACTIVE     = FALSE;
    protected $IS_OLD        = FALSE;
    protected $OLD_VERSION   = '';
    protected $ERROR_MESSAGE = '';

    public function __construct(){
        parent::__construct();
        $query = $this->db->select('version')
            ->from(cms_table_name('main_module'))
            ->where(array(
                'module_name'=> $this->NAME,
                'module_path'=> $this->cms_module_path(),
              ))
            ->get();
        if ($query->num_rows() == 0) {
            $this->IS_ACTIVE = FALSE;
            $this->IS_OLD = FALSE;
            $this->OLD_VERSION = '0.0.0';
        } else {
            $this->IS_ACTIVE = TRUE;
            $row = $query->row();
            $this->OLD_VERSION = $row->version;
            if($this->OLD_VERSION == ''){
                $this->OLD_VERSION = '0.0.0';
            }
            if(version_compare($this->VERSION, $this->OLD_VERSION)>0){
                $this->IS_OLD = TRUE;
            }else{
                $this->IS_OLD = FALSE;
            }
        }
    }

    public function status(){
        $result = array(
            'active'=>$this->IS_ACTIVE,
            'old'=>$this->IS_OLD,
            'description'=>$this->DESCRIPTION,
            'dependencies'=>$this->DEPENDENCIES,
            'name'=>$this->NAME,
            'version'=>$this->VERSION,
            'old_version'=>$this->OLD_VERSION,
        );
        if($this->input->is_ajax_request()){
            $this->cms_show_json($result);
        }else{
            $this->cms_show_variable($result);
        }
    }

    public final function index()
    {
        if ($this->cms_is_module_active($this->NAME)) {
            $this->deactivate();
        } else {
            $this->activate();
        }
    }

    public final function activate()
    {
        // login (in case of called from No-CMS installer)
        $silent   = $this->input->post('silent');
        $identity = $this->input->post('identity');
        $password = $this->input->post('password');
        if ($identity && $password) {
            $this->cms_do_login($identity, $password);
        }

        $result = array(
            'success'      => TRUE,
            'message'      => array(),
            'module_name'  => $this->NAME,
            'module_path'  => $this->cms_module_path(),
            'dependencies' => $this->DEPENDENCIES,
        );

        // check for error
        if (!$this->cms_have_privilege('cms_install_module')) {
            $result['message'][] = 'Not enough privilege';
            $result['success']   = FALSE;
        }else{
            if($this->NAME == ''){
                $result['message'][] = 'Module name is undefined';
                $result['success']   = FALSE;
            }
            if($this->IS_ACTIVE){
                $result['message'][] = 'The module is already activated';
                $result['success']   = FALSE;
            }
            foreach ($this->DEPENDENCIES as $dependency) {
                if (!$this->cms_is_module_active($dependency)) {
                    $result['message'][] = 'Dependency error '.br().'Please activate these module first:'.ul($this->DEPENDENCIES);
                    $dependencies_error  = TRUE;
                    $result['success']   = FALSE;
                    break;
                }
            }
        }

        // try to activate
        if($result['success']){
            $this->db->trans_start();
            if($this->do_activate() !== FALSE){
                $this->register_module();
            }else{
                $result['success']   = FALSE;
                if($this->ERROR_MESSAGE != ''){
                    $result['message'][] = $this->ERROR_MESSAGE;
                }else{
                    $result['message'][] = 'Failed to activate module';
                }
            }
            $this->db->trans_complete();
        }

        $result['message'] = ul($result['message']);

        // show result
        if($silent){
            $this->cms_show_json($result);
        } else if($result['success']) {
            redirect('main/module_management');
        } else {
            $this->view('main/module_activation_error', $result, 'main_module_management');
        }
    }

    public final function deactivate()
    {
        $result = array(
            'success'      => TRUE,
            'message'      => array(),
            'module_name'  => $this->NAME,
            'module_path'  => $this->cms_module_path(),
            'dependencies' => array(),
        );

        // check for error
        if (!$this->cms_have_privilege('cms_install_module')) {
            $result['message'][] = 'Not enough privilege';
            $result['success']   = FALSE;
        } else {
            $children                = $this->child_module();
            if ($this->NAME == '') {
                $result['message'][] = 'Module name is undefined';
                $result['success']   = FALSE;
            }
            if (!$this->IS_ACTIVE) {
                $result['message'][] = 'The module is already deactivated';
                $result['success']   = FALSE;
            }
            if (count($children) != 0) {
                $result['message'][] = 'Dependency error '.br().'Please deactivate these module first:'.ul($this->children);
                $dependencies_error  = TRUE;
                $result['success']   = FALSE;
                break;
            }
        }

        // try to deactivate
        if($result['success']){
            $this->db->trans_start();
            if($this->do_deactivate() !== FALSE){
                $this->unregister_module();
            }else{
                $result['success']   = FALSE;
                if($this->ERROR_MESSAGE != ''){
                    $result['message'][] = $this->ERROR_MESSAGE;
                }else{
                    $result['message'][] = 'Failed to deactivate module';
                }
            }
            $this->db->trans_complete();
        }

        $result['message'] = ul($result['message']);

        if($result['success']) {
            redirect('main/module_management');
        } else {
            $this->view('main/module_deactivation_error', $result, 'main_module_management');
        }
    }

    public final function upgrade()
    {
        $result = array(
            'success'      => TRUE,
            'message'      => array(),
            'module_name'  => $this->NAME,
            'module_path'  => $this->cms_module_path(),
            'dependencies' => array(),
        );
        if (!$this->cms_have_privilege('cms_install_module')) {
            $result['message'][] = 'Not enough privilege';
            $result['success']   = FALSE;
        }else{
            if ($this->NAME == '') {
                $result['message'][] = 'Module name is undefined';
                $result['success']   = FALSE;
            }
            if (!$this->IS_ACTIVE) {
                $result['message'][] = 'The module is inactive';
                $result['success']   = FALSE;
            }
        }
        if($result['success']){
            $this->db->trans_start();
            if($this->do_upgrade() !== FALSE){
                $data  = array('version' => $this->VERSION);
                $where = array('module_name' => $this->NAME);
                $this->db->update(cms_table_name('main_module'), $data, $where);
                $this->db->trans_complete();
            }else{
                $result['success']   = FALSE;
                if($this->ERROR_MESSAGE != ''){
                    $result['message'][] = $this->ERROR_MESSAGE;
                }else{
                    $result['message'][] = 'Failed to upgrade module';
                }
            }
        }

        $result['message'] = ul($result['message']);

        if($result['success']) {
            redirect('main/module_management');
        } else {
            $this->view('main/module_upgrade_error', $result, 'main_module_management');
        }
    }

    public function setting(){
        $data['cms_content'] = '<p>Setting is not available</p>'.anchor(site_url('main/module_management'),'Back');
        $this->view('CMS_View',$data,'main_module_management');
    }

    protected function do_install()
    {
        // deprecated function, please use do_activate instead
        return FALSE;
    }
    protected function do_uninstall()
    {
        // deprecated function, please use do_deactivate instead
        return FALSE;
    }

    protected function do_activate()
    {
        //this should be overridden by module developer
        return $this->do_install();
    }

    protected function do_deactivate()
    {
        //this should be overridden by module developer
        return $this->do_uninstall();
    }

    protected function do_upgrade($old_version)
    {
        //this should be overridden by module developer
        return FALSE;
    }

    protected final function execute_SQL($SQL, $separator)
    {
        $queries = explode($separator, $SQL);
        foreach ($queries as $query) {
            if(trim($query) == '') continue;
            $table_prefix = cms_module_table_prefix($this->cms_module_path());
            $module_prefix = cms_module_prefix($this->cms_module_path());
            $query = preg_replace('/\{\{ complete_table_name:(.*) \}\}/si', $table_prefix==''? '$1': $table_prefix.'_'.'$1', $query);
            $query = preg_replace('/\{\{ module_prefix \}\}/si', $module_prefix, $query);
            $this->db->query($query);
        }
    }
    protected final function add_navigation($navigation_name, $title, $url, $authorization_id = 1, $parent_name = NULL, $index = NULL, $description = NULL)
    {
        //get parent's navigation_id
        $SQL   = "SELECT navigation_id FROM ".cms_table_name('main_navigation')." WHERE navigation_name='" . addslashes($parent_name) . "'";
        $query = $this->db->query($SQL);
        $row   = $query->row();

        $parent_id = isset($row->navigation_id) ? $row->navigation_id : NULL;

        //if it is null, index = max index+1
        if (!isset($index)) {
            if (isset($parent_id)) {
                $whereParentId = "(parent_id = $parent_id)";
            } else {
                $whereParentId = "(parent_id IS NULL)";
            }
            $SQL   = "SELECT max(`index`)+1 AS newIndex FROM `".cms_table_name('main_navigation')."` WHERE $whereParentId";
            $query = $this->db->query($SQL);
            $row   = $query->row();
            $index = $row->newIndex;
            if (!isset($index))
                $index = 0;
        }

        //insert it :D
        $data = array(
            "navigation_name" => $navigation_name,
            "title" => $title,
            "url" => $url,
            "authorization_id" => $authorization_id,
            "index" => $index,
            "description" => $description
        );
        if (isset($parent_id)) {
            $data['parent_id'] = $parent_id;
        }
        $this->db->insert(cms_table_name('main_navigation'), $data);
    }
    protected final function remove_navigation($navigation_name)
    {
        //get navigation_id
        $SQL           = "SELECT navigation_id FROM ".cms_table_name('main_navigation')." WHERE navigation_name='" . addslashes($navigation_name) . "'";
        $query         = $this->db->query($SQL);
        $row           = $query->row();
        $navigation_id = isset($row->navigation_id) ? $row->navigation_id : NULL;

        if (isset($navigation_id)) {
            //delete quicklink
            $where = array(
                "navigation_id" => $navigation_id
            );
            $this->db->delete(cms_table_name('main_quicklink'), $where);
            //delete cms_group_navigation
            $where = array(
                "navigation_id" => $navigation_id
            );
            $this->db->delete(cms_table_name('main_group_navigation'), $where);
            //delete cms_navigation
            $where = array(
                "navigation_id" => $navigation_id
            );
            $this->db->delete(cms_table_name('main_navigation'), $where);
        }
    }
    protected final function add_privilege($privilege_name, $title, $authorization_id = 1, $parent_name = NULL, $description = NULL)
    {
        $data = array(
            "privilege_name" => $privilege_name,
            "title" => $title,
            "authorization_id" => $authorization_id,
            "description" => $description
        );
        $this->db->insert(cms_table_name('main_privilege'), $data);
    }
    protected final function remove_privilege($privilege_name)
    {
        $SQL   = "SELECT privilege_id FROM ".cms_table_name('main_privilege')." WHERE privilege_name='" . addslashes($privilege_name) . "'";
        $query = $this->db->query($SQL);

        foreach ($query->result() as $row) {
            $privilege_id = $row->privilege_id;
        }

        if (isset($privilege_id)) {
            //delete cms_group_privilege
            $where = array(
                "privilege_id" => $privilege_id
            );
            $this->db->delete(cms_table_name('main_group_privilege'), $where);
            //delete cms_privilege
            $where = array(
                "privilege_id" => $privilege_id
            );
            $this->db->delete(cms_table_name('main_privilege'), $where);
        }
    }

    private final function register_module()
    {
        //insert to cms_module
        $data = array(
            'module_name' => $this->NAME,
            'module_path' => $this->cms_module_path(),
            'version'     => $this->VERSION,
            'user_id'     => $this->cms_user_id()
        );
        $this->db->insert(cms_table_name('main_module'), $data);

        //get current cms_module_id as child_id
        $SQL      = "SELECT module_id FROM ".cms_table_name('main_module')." WHERE module_name='" . addslashes($this->NAME) . "'";
        $query    = $this->db->query($SQL);
        $row      = $query->row();
        $child_id = $row->module_id;

        //get parent_id
        if (isset($child_id)) {
            foreach ($this->DEPENDENCIES as $dependency) {
                $SQL       = "SELECT module_id FROM ".cms_table_name('main_module')." WHERE module_name='" . addslashes($dependency) . "'";
                $query     = $this->db->query($SQL);
                $row       = $query->row();
                $parent_id = $row->module_id;
                $data      = array(
                    "parent_id" => $parent_id,
                    "child_id" => $child_id
                );
                $this->db->insert(cms_table_name('main_module_dependency'), $data);

            }
        }

    }
    private final function unregister_module()
    {
        //get current cms_module_id as child_id
        $SQL      = "SELECT module_id FROM ".cms_table_name('main_module')." WHERE module_name='" . addslashes($this->NAME) . "'";
        $query    = $this->db->query($SQL);
        $row      = $query->row();
        $child_id = $row->module_id;

        $where = array(
            'child_id' => $child_id
        );
        $this->db->delete(cms_table_name('main_module_dependency'), $where);

        $where = array(
            'module_path' => $this->cms_module_path()
        );
        $this->db->delete(cms_table_name('main_module'), $where);
    }

    private final function child_module()
    {
        $SQL   = "SELECT module_id FROM ".cms_table_name('main_module')." WHERE module_name='" . addslashes($this->NAME) . "'";
        $query = $this->db->query($SQL);
        $row   = $query->row();
        if ($query->num_rows() > 0) {
            $parent_id = $row->module_id;

            $SQL    = "
	            SELECT module_name, module_path
	            FROM
	                ".cms_table_name('main_module_dependency').",
	                ".cms_table_name('main_module')."
	            WHERE
	                module_id = child_id AND
	                parent_id=" . $parent_id;
            $query  = $this->db->query($SQL);
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = array(
                    "module_name" => $row->module_name,
                    "module_path" => $row->module_name
                );
            }
            return $result;
        } else {
            return array();
        }
    }

    protected final function add_widget($widget_name, $title, $authorization_id = 1, $url = NULL, $slug = NULL, $index = NULL, $description = NULL)
    {
        //if it is null, index = max index+1
        if (!isset($index)) {
            if (isset($slug)) {
                $whereSlug = "(slug = '$slug')";
            } else {
                $whereSlug = "(slug IS NULL)";
            }
            $SQL   = "SELECT max(`index`)+1 AS newIndex FROM `".cms_table_name('main_widget')."` WHERE $whereSlug";
            $query = $this->db->query($SQL);
            $row   = $query->row();
            $index = $row->newIndex;

            if (!isset($index))
                $index = 0;
        }

        $data = array(
            "widget_name" => $widget_name,
            "title" => $title,
            "slug" => $slug,
            "index" => $index,
            "authorization_id" => $authorization_id,
            "url" => $url,
            "description" => $description
        );
        $this->db->insert(cms_table_name('main_widget'), $data);
    }
    protected final function remove_widget($widget_name)
    {
        $SQL       = "SELECT widget_id FROM ".cms_table_name('main_widget')." WHERE widget_name='" . addslashes($widget_name) . "'";
        $query     = $this->db->query($SQL);
        $row       = $query->row();
        $widget_id = $row->widget_id;

        if (isset($widget_id)) {
            //delete cms_group_privilege
            $where = array(
                "widget_id" => $widget_id
            );
            $this->db->delete(cms_table_name('main_group_widget'), $where);
            //delete cms_privilege
            $where = array(
                "widget_id" => $widget_id
            );
            $this->db->delete(cms_table_name('main_widget'), $where);
        }
    }

    protected final function add_quicklink($navigation_name)
    {
        $SQL   = "SELECT navigation_id FROM ".cms_table_name('main_navigation')." WHERE navigation_name ='" . addslashes($navigation_name) . "'";
        $query = $this->db->query($SQL);
        if ($query->num_rows() > 0) {
            $row           = $query->row();
            $navigation_id = $row->navigation_id;
            // index = max index+1
            $SQL           = "SELECT max(`index`)+1 AS newIndex FROM `".cms_table_name('main_quicklink')."`";
            $query         = $this->db->query($SQL);
            $row           = $query->row();
            $index         = $row->newIndex;
            if (!isset($index))
                $index = 0;

            // insert
            $data = array(
                "navigation_id" => $navigation_id,
                "index" => $index
            );
            $this->db->insert(cms_table_name('main_quicklink'), $data);
        }
    }

    protected final function remove_quicklink($navigation_name)
    {
        $SQL   = "SELECT navigation_id FROM ".cms_table_name('main_navigation')." WHERE navigation_name ='" . addslashes($navigation_name) . "'";
        $query = $this->db->query($SQL);
        if ($query->num_rows() > 0) {
            $row           = $query->row();
            $navigation_id = $row->navigation_id;

            // delete
            $where = array(
                "navigation_id" => $navigation_id
            );
            $this->db->delete(cms_table_name('main_quicklink'), $where);
        }
    }
}

class MY_Controller extends CMS_Controller
{
}