<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL(); ?>>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta name="theme-color" content="#424242" />
		<link rel="stylesheet" href="<?php echo base_url('theme.css'); ?>">
		 <script>
        const rawThemeSettings = <?php echo json_encode($this->customlib->getCurrentThemeSetting() ?? new stdClass()); ?>;
        const themeSettings = rawThemeSettings || {};
        (function() {
            const themeBackground = themeSettings.theme_background || 'light-mode';
            const savedLayout = themeSettings.theme_content || 'container-fluid';
            const themeShadow = themeSettings.theme_shadow || '';
            const themeColor = themeSettings.theme_color || '#7367f0';
            const themeNavigation = themeSettings.theme_navigation || "expanded";
            const themeClass = themeBackground === 'dark' ? 'dark' : 'light-mode';
            const themeClassNavigation = themeNavigation === 'collapsed' ? 'sidebar-collapse' : '';
            function applyBodyClasses() {
                if (!document.body) return;
                const classList = document.body.classList;
                classList.remove('light-mode', 'dark', 'sidebar-collapse');
                [...classList].forEach(cls => {
                    if (cls.startsWith('shadow')) classList.remove(cls);
                });
                classList.add(themeClass);
                if (themeShadow) classList.add(themeShadow);
                if (themeClassNavigation) classList.add(themeClassNavigation);
            }
            function applyLayoutAndIcons() {
                const content = document.querySelector("section.content");
                const layoutIcon = document.getElementById('content-icon');
                const shadowIcon = document.getElementById('iconskins');
                const navigationIcon = document.getElementById('icon_theme_navigation');
                if (content && !content.classList.contains(savedLayout)) content.classList.add(savedLayout);
                if (navigationIcon) {
                    const isCollapsed = themeClassNavigation === 'sidebar-collapse';
                    navigationIcon.classList.toggle('fa-bars', isCollapsed);
                    navigationIcon.classList.toggle('fa-brands', !isCollapsed);
                    navigationIcon.classList.toggle('fa-elementor', !isCollapsed);
                }
                if (layoutIcon) {
                    layoutIcon.classList.toggle('fa-compress', savedLayout === 'container-fluid');
                    layoutIcon.classList.toggle('fa-expand', savedLayout !== 'container-fluid');
                }
                if (shadowIcon) {
                    shadowIcon.classList.toggle('fa-border-none', themeShadow === 'shadow-applied');
                    shadowIcon.classList.toggle('fa-border-all', themeShadow !== 'shadow-applied');
                }
                if (content || layoutIcon || shadowIcon) layoutObserver.disconnect();
            }
            if (document.body) applyBodyClasses();
            else new MutationObserver((mutations, observer) => {
                if (document.body) { applyBodyClasses(); observer.disconnect(); }
            }).observe(document.documentElement, {childList: true});
            const layoutObserver = new MutationObserver(applyLayoutAndIcons);
            layoutObserver.observe(document.documentElement, {childList: true, subtree: true});
            applyLayoutAndIcons();
        })();
    </script>
    <?php $this->load->view('layout/theme-color'); ?>
        <link href="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>" rel="shortcut icon" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/jquery.mCustomScrollbar.min.css">
        <?php $this->load->view('layout/theme'); ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ss-print.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/all.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/dropify.min.css">
        <link href="<?php echo base_url(); ?>backend/dist/css/nprogress.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>backend/dist/css/bootstrap-select.min.css">
		 <link rel="stylesheet" href="https://icons.getbootstrap.com/assets/font/bootstrap-icons.min.css" />
        <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
        <script language="javascript" src="<?php echo base_url(); ?>backend/custom/jquery-2.2.4.js"></script>
        <script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
         <link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
        <script src="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.js"></script>
        <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/js/school-custom.js"></script>
        <script src="<?php echo base_url(); ?>backend/js/school-admin-custom.js"></script>
        <script src="<?php echo base_url(); ?>backend/js/sstoast.js"></script>
        <script src="<?php echo base_url(); ?>backend/js/export_lib.js"></script>
        <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.print.min.css" media="print">
        <link rel="stylesheet" href="<?php echo base_url() ?>backend/dist/css/lucide-static@0.543.0/lucide.css">
        <script type="text/javascript">
            var baseurl = "<?php echo base_url(); ?>";
            var start_week=<?php echo $this->customlib->getStartWeek(); ?>;
        </script>
        <style type="text/css">
		span.flag-icon.flag-icon-us{ text-orientation: mixed; }
        </style>
    </head>
	
    <body class="hold-transition skin-blue sidebar-mini">
       <div class="wrapper">
			 <?php $result = $this->customlib->getLoggedInUserData(); ?>
            <header class="main-header" id="alert">
                <a href="<?php echo base_url(); ?>admin/admin/dashboard" class="logo">
                    <span class="logo-mini"><img src="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo() . img_time();?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
                    <span class="logo-lg"><img src="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo() . img_time();?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a onclick="collapseSidebar()" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only"><?php echo $this->lang->line('toggle_navigation'); ?></span>
                        <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                    </a>
                    <div class="col-lg-4 col-md-3 col-sm-2 col-xs-4">
                        <span href="#" class="sidebar-session"><?php echo $this->setting_model->getCurrentSchoolName(); ?></span>
                    </div>
                    <div class="col-lg-8 col-md-9 col-sm-10 col-xs-8">
                        <div class="pull-right">
                            <?php if ($this->rbac->hasPrivilege('student', 'can_view')) {?>
                                <form id="header_search_form" class="navbar-form navbar-left search-form" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="input-group">
                                        <input type="text" value="<?php echo set_value('search_text1'); ?>" name="search_text1" id="search_text1" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                        <span class="input-group-btn"><button type="submit" name="search" id="search-btn" onclick="getstudentlist()" class="btn btn-flat topsidesearchbtn"><i class="fa fa-search"></i></button></span>
                                    </div>
                                </form>
                            <?php }?>
                            <div class="navbar-custom-menu">
                                <ul class="nav navbar-nav headertopmenu">
                                    <li class="cal15" data-placement="bottom" data-toggle="tooltip" title="Application Activation"><a href="<?php echo site_url('admin/activation'); ?>"><i class="fa fa-key"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
