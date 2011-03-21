<?php

	class extension_dashboard_analytics extends Extension {
		private $params = array();

		public function about() {
			return array(
				'name'			=> 'Dashboard Analytics',
				'version'		=> '1.0',
				'release-date'	=> '2011-03-14',
				'author'		=> array(
					'name'			=> 'Nick Ryall',
					'website'		=> 'http://randb.com.au/',
					'email'			=> 'nick@randb.com.au'
				),
				'description'	=> 'Uses Nick Dunn\'s Dashboard Extension to display analytics information in a custom panel'
	 		);
		}
		
		
		public function getSubscribedDelegates() {
		    return array(
		        array(
		        	'page'		=> '/backend/',
		        	'delegate'	=> 'InitaliseAdminPageHead',
		        	'callback'	=> 'append_assets'
		        ),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelRender',
		            'callback'  => 'render_panel'
		        ),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelOptions',
		            'callback'  => 'dashboard_panel_options'
		        ),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelTypes',
		            'callback'  => 'dashboard_panel_types'
		        ),
		    );
		}
		
		
		public function dashboard_panel_types($context) {
		    $context['types']['analytics_panel'] = 'Google Analytics Panel';
		}
		
		
		public function dashboard_panel_options($context) {
		    // make sure it's your own panel type, as this delegate fires for all panel types!
		    if ($context['type'] != 'analytics_panel') return;
		
		    $config = $context['existing_config'];
		
		    $fieldset = new XMLElement('fieldset', NULL, array('class' => 'settings'));
		    $fieldset->appendChild(new XMLElement('legend', 'My Google Analytics Account'));
		
		    $email = Widget::Label('Email', Widget::Input('config[ga-email]', $config['ga-email']));
		    $fieldset->appendChild($email);
		    
		    $password = Widget::Label('Password', Widget::Input('config[ga-password]', $config['ga-password'], 'password'));
		    $fieldset->appendChild($password);
		    
		    $profile_id = Widget::Label('Profile ID', Widget::Input('config[ga-profile-id]', $config['ga-profile-id']));
		    $fieldset->appendChild($profile_id);
		
		    $context['form'] = $fieldset;
		
		}
		
		
		public function render_panel($context) {
		    if ($context['type'] != 'analytics_panel') return;
		    $config = $context['config'];
		    $context['panel']->appendChild(extension_dashboard_analytics::display_results($config['ga-email'], $config['ga-password'], $config['ga-profile-id']));
		}

		public function display_results($email, $password, $profile_id) {
		
			
			require 'lib/analytics.class.php';
			
			// session_start for caching
			session_start();
			
			try {
			    
			    // construct the class
			    $oAnalytics = new analytics($email, $password);
			    
			    // set it up to use caching
			    $oAnalytics->useCache();
			    
			    //$oAnalytics->setProfileByName('[Google analytics accountname]');
			    $oAnalytics->setProfileById('ga:'.$profile_id);
			    
			    // set the date range
			    $last_month = date("Y-m-d", strtotime('today - 30 days'));
			    $today = date("Y-m-d", strtotime('today'));
			    //$oAnalytics->setMonth(date('n'), date('Y'));
			    $oAnalytics->setDateRange($last_month, $today);
			    
			    
			    $wrapper = new XMLElement('div');
			    $wrapper->setAttribute('class', 'google_analytics');
			    $dl_results = new XMLElement('dl');
			    
			    
			    //Total Pageviews
			    $dt_pageviews = new XMLElement('dt', 'Total Pageviews');
			    $dd_pageviews = new XMLElement('dd', array_sum($oAnalytics->getPageviews()));
			    
			    $dl_results->appendChild($dt_pageviews);
			    $dl_results->appendChild($dd_pageviews);
			    
			    
			    //Total Visits
			    $dt_visits = new XMLElement('dt', 'Total Visits');
			    $dd_visits = new XMLElement('dd', array_sum($oAnalytics->getVisitors()));
			    
			    $dl_results->appendChild($dt_visits);
			    $dl_results->appendChild($dd_visits);
			    
			    $wrapper->appendChild($dl_results);
			    
			    $graph = extension_dashboard_analytics::buildChart($oAnalytics);
			    
  				$wrapper->appendChild($graph);
			    return $wrapper;

			    
			} catch (Exception $e) { 
			    echo 'Caught exception: ' . $e->getMessage(); 
			} 
				
		}
		
		
		public function buildChart($oAnalytics) {
		
			require 'lib/google_chart.php'; // By Andrey Savchenko (Rarst), http://www.rarst.net/script/google-chart/
			
			
			// Generating visit arrays for the date range.
			 $visit_report = $oAnalytics->getData(
			 	array('dimensions'=>urlencode('ga:date'),
			 	'metrics'=>urlencode('ga:visits'),
			 ));
			 
			 
			 $visits = array();
			 foreach($visit_report as $dimensions => $metric) {
			 	array_push($visits, $metric);
			 }

			 
			 // Generating visit arrays for the date range.
			  $views_report = $oAnalytics->getData(
			  	array('dimensions'=>urlencode('ga:date'),
			  	'metrics'=>urlencode('ga:pageviews'),
			  ));
			  
			  
			  $page_views = array();
			  foreach($views_report as $dimensions => $metric) {
			  	array_push($page_views, $metric);
			  }
			
			// Extract various dates from the report array keys in order to use them as variables for x-axis labels
			$days = array_keys($views_report);
			list($d0, $d1, $d2, $d3, $d4, $d5, $d6, $d7, $d8, $d9, $d10, $d11, $d12, $d13,
			$d14, $d15, $d16, $d17, $d18, $d19, $d20, $d21,$d22,$d23,$d24,$d25,$d26,$d27,$d28, $d29, $d30) = $days;
			
			
			// Get the keys for max. values of page views and visits
			function max_key($array) {
			 foreach ($array as $key => $val) {
			 if ($val == max($array)) return $key;
			 }
			}
			$array = $page_views;
			$precord = max_key($array);
			$array = $visits;
			$vrecord = max_key($array);
			
			// Always use max. value recorded in array for y-axis
			$ymax = 1*(ceil(max($page_views)));
			// Devide it by six and round up to nearest whole number to set appropriate y-axis ticks
			$ytick = ceil((max($page_views))/6);
			
			// Chart settings
			// Chart settings
			$traffic = new GoogleChart;
			$traffic->type='lc';
			$traffic->SetImageSize(550,300);
			$traffic->SetChartMargins(40,40,40,40);
			$traffic->SetEncode('simple');
			$traffic->AddData($visits);
			$traffic->AddData($page_views);
			
			$traffic->AddChartColor('FF9900');
			$traffic->AddChartColor('0077CC');
			
			$traffic->AddLineStyle(3);
			$traffic->AddLineStyle(3);
			
			$traffic->AddFillArea('B','FF99007F',0);
			$traffic->AddFillArea('b','E6F2FA7F',0,1);

			$traffic->AddAxis('y,x');
			$traffic->AddAxisRange(0,round($ymax,-3),round($ytick, -3));
			$traffic->AddAxisLabel(extension_dashboard_analytics::formatDates(array($d0,$d10,$d20,$d30)),1);

			$traffic->SetGrid(round(100/30,2),round(100/6,2),1,3);
			
			
			$traffic->SetTitle('Visits/Page Views of Last 30 Days');
			$traffic->AddLegend('visits');
			$traffic->AddLegend('page views');
			

			// Generate chart URL
	
			
			$graph = new XMLElement('div', $traffic->GetImg());
			return $graph;
		
		}
		
		/*-------------------------------------------------------------------------
			Utitilites:
		-------------------------------------------------------------------------*/
		
		public function formatDates($dates) {
		
			$formatted_dates = array();
			foreach($dates as $date) {
				
				$date = date("Y-m-d", strtotime(str_replace('ga:date=', '', $date)));
				
				array_push($formatted_dates, $date);
			
			}
			
			return $formatted_dates;
		
		}
		
		
		public function append_assets($context) {
			$page = $context['parent']->Page;
			$page->addStylesheetToHead(URL . '/extensions/dashboard_analytics/assets/dashboard.analytics.index.css', 'screen', 1000);
		}

	}	
?>