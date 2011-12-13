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
		
			
			require_once 'lib/analytics.class.php';
			
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
			    
			    $graph = extension_dashboard_analytics::buildChart($oAnalytics);
				$wrapper->appendChild($graph);
			    
			    $info = new XMLElement('div');
			    $info->setAttribute('class', 'info');
			    $info_header = new XMLElement('h4', 'Quick Information');
			    $dl_results = new XMLElement('dl');
			    
			    //Total Pageviews
			    $dt_pageviews = new XMLElement('dt', 'Pageviews');
			    $dd_pageviews = new XMLElement('dd', array_sum($oAnalytics->getPageviews()));
			    
			    $dl_results->appendChild($dt_pageviews);
			    $dl_results->appendChild($dd_pageviews);
			    
			    //Total Visits
			    $dt_visits = new XMLElement('dt', 'Visits');
			    $dd_visits = new XMLElement('dd', array_sum($oAnalytics->getVisitors()));
			    
			    $dl_results->appendChild($dt_visits);
			    $dl_results->appendChild($dd_visits);
			    
			    //Pages/Visit
			    $pages_visits = $oAnalytics->getData(
			     	array('metrics'=> urlencode('ga:pageviewsPerVisit'))
			    );
		     	$dt_pages_visits = new XMLElement('dt', 'Pages per Visit');
		     	$dd_pages_visits = new XMLElement('dd',  round(array_sum($pages_visits),2));
		     	$dl_results->appendChild($dt_pages_visits);
		     	$dl_results->appendChild($dd_pages_visits);
			    
			    $bounce_rate = $oAnalytics->getData(
			    	array('metrics'=> urlencode('ga:visitBounceRate'))
			    );
			    $dt_bounce_rate = new XMLElement('dt', 'Bounce Rate');
			    $dd_bounce_rate = new XMLElement('dd', round(array_sum($bounce_rate),2).'%');
			    $dl_results->appendChild($dt_bounce_rate );
			    $dl_results->appendChild($dd_bounce_rate);
			    
			    //% New Visits
			    $new_visits = $oAnalytics->getData(
			    	array('metrics'=> urlencode('ga:percentNewVisits'))
			    );
			  	$dt_new_visits = new XMLElement('dt', '% New Visits');
			  	$dd_new_visits = new XMLElement('dd',  round(array_sum($new_visits),2).'%');
			  	$dl_results->appendChild($dt_new_visits);
			  	$dl_results->appendChild($dd_new_visits);
			  	
			  	
			  	//Avg Time on Site
			  	$average_time = $oAnalytics->getData(
			  		array('metrics'=> urlencode('ga:avgTimeOnSite'))
			  	);
		  		$dt_average_time = new XMLElement('dt', 'Avg. Time on Site');
		  		$dd_average_time = new XMLElement('dd',  extension_dashboard_analytics::sec2hms(round(array_sum($average_time),0)));
		  		$dl_results->appendChild($dt_average_time);
		  		$dl_results->appendChild($dd_average_time);
			  	

				$search_terms = new XMLElement('div');
			    $search_terms->setAttribute('class', 'terms');
			    
			    //Search Terms
			    $terms_head = new XMLElement('h4', 'Top Keywords');
			    $terms = new XMLElement('ol');
			    $keywords = array_keys($oAnalytics->getSearchWords());

			    $count = 0;
			    foreach($keywords as $keyword) {	
			    	$item = new XMLElement('li', $keyword);
			    	$terms->appendChild($item);
			    	$count++;
			    	if ($count == 10) break;
			    }
			
			    $info->appendChild($info_header);
			    $info->appendChild($dl_results);
			
			    $search_terms->appendChild($terms_head);
			    $search_terms->appendChild($terms);
			
			    $wrapper->appendChild($info);
			$wrapper->appendChild($search_terms);

			    return $wrapper;

			    
			} catch (Exception $e) { 
			   
			   $info = new XMLElement('div');
			   $info->setAttribute('class', 'info');
			   $info_header = new XMLElement('h4', 'No data found. Check your account details.');
			   $info->appendChild($info_header);
			   $info_header->appendChild(new XMLElement('p', '<code>'.(string)$e->getMessage().'</code>'));
			   return $info;

			   
			} 
				
		}
		
		
		public function buildChart($oAnalytics) {
		
			require_once 'lib/google_chart.php'; // By Andrey Savchenko (Rarst), http://www.rarst.net/script/google-chart/
			
			
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
			if( !function_exists('max_key') ){
			 function max_key($array) {
			  foreach ($array as $key => $val) {
			   if ($val == max($array)) return $key;
			  }
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
			$traffic = new GoogleChart;
			$traffic->type='lc';
			$traffic->SetImageSize(700,200);
			$traffic->SetChartMargins(20,20,20,20);
			$traffic->SetEncode('simple');
			$traffic->AddData($visits);
			$traffic->AddData($page_views);
			
			$traffic->AddChartColor('FF9900');
			$traffic->AddChartColor('0077CC');
			
			$traffic->AddLineStyle(3);
			$traffic->AddLineStyle(3);

			
			$traffic->AddFillArea('B','FF99007F',0);
			$traffic->AddFillArea('b','E6F2FA7F',0,1);
			
			$traffic->AddShapeMarker('o','FFFFFF',0,-1,9);
			$traffic->AddShapeMarker('o','FF9900',0,-1,7);
			$traffic->AddShapeMarker('o','FFFFFF',1,-1,9);
			$traffic->AddShapeMarker('o','0077CC',1,-1,7);

			
			$traffic->AddAxis('y,x');
			$traffic->AddAxisRange(0,round($ymax,-3),round($ytick, -3));
			$traffic->AddAxisLabel(extension_dashboard_analytics::formatDates(array($d0,$d10,$d20,$d30)),1);

			$traffic->SetGrid(round(100/30,2),round(100/6,2),1,3);
			
			
			$traffic->SetTitle('Visits and Page Views of last 30 days');
			$traffic->AddLegend('visits');
			$traffic->AddLegend('page views');
			$traffic->SetLegendPosition('b');
			
			

			// Generate chart URL
	
			
			$graph = new XMLElement('div', $traffic->GetImg());
			$graph->setAttribute('class', 'graph');
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
		
		
		
		public function sec2hms ($sec, $padHours = false) 
		  {
		
		    // start with a blank string
		    $hms = "";
		    
		    // do the hours first: there are 3600 seconds in an hour, so if we divide
		    // the total number of seconds by 3600 and throw away the remainder, we're
		    // left with the number of hours in those seconds
		    $hours = intval(intval($sec) / 3600); 
		
		    // add hours to $hms (with a leading 0 if asked for)
		    $hms .= ($padHours) 
		          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
		          : $hours. ":";
		    
		    // dividing the total seconds by 60 will give us the number of minutes
		    // in total, but we're interested in *minutes past the hour* and to get
		    // this, we have to divide by 60 again and then use the remainder
		    $minutes = intval(($sec / 60) % 60); 
		
		    // add minutes to $hms (with a leading 0 if needed)
		    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
		
		    // seconds past the minute are found by dividing the total number of seconds
		    // by 60 and using the remainder
		    $seconds = intval($sec % 60); 
		
		    // add seconds to $hms (with a leading 0 if needed)
		    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		
		    // done!
		    return $hms;
		    
		  }
		

	}	
?>