<?php
/*
Plugin Name: R Google Chart
Plugin URI: http://www.Rarst.net/script/google-chart/
Description: Class that provides PHP wrap for Google Chart API
Version: 0.2
Author: Rarst
Author URI: http://www.Rarst.net/
*/
class GoogleChart {
	var $api = 'http://chart.apis.google.com/chart?';
	var $simple;
	var $extended;
	
	var $data;
		var $encode;
		var $yMin;
		var $yMax;
		var $xMin;
		var $xMax;
		var $scaledData;
		var $scaleFactor;
	var $type;
	var $width;
	var $height;
	
	var $chartColor;
	var $chf;
	
	var $title;
		var $rawTitle;
	var $label;
	var $legend;
	var $legendPosition;
	
	var $axis;
	var $axisLabel;
	var $axisLabelPos;
	var $axisRange;
	var $axisStyle;
	var $axisTickLength;
	
	var $barStyle;
	var $barZeroLine;
	var $chartMargins;
	var $lineStyle;
	var $grid;
	var $chm;

	var $chem;
	
	/**
	 * constructor
	 */
	function GoogleChart() {
		$this->simple = str_split( 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' );
		$this->extended = str_split( 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.' );
	}
	
	/**
	 * Visible Axes chxt
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_type
	 *
	 * @param string|array $input x (bottom), t (top), y (left), r (right)
	 */
	function AddAxis( $input ) {
		if ( is_array( $input ) )
			foreach( $input as $axis )
				$this->axis[] = $axis;
		else
			$this->axis[] = $input;
	}
	
	/**
	 * Custom Axis Labels chxl
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_labels
	 *
	 * @param array $array of strings for axis labels
	 * @param integer $index of axis, zero-based
	 */
	function AddAxisLabel( $array, $index = false ) {
		$index ? $this->axisLabel[$index] = implode( '|', $array ) : $this->axisLabel[] = implode( '|', $array ) ;
	}
	
	/**
	 * Axis Label Positions chxp
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_label_positions
	 *
	 * @param array $array of numeric positions, as many as labels, 0-100 default range
	 * @param integer $index of axis, zero-based
	 */
	function AddAxisLabelPosition( $array, $index = false ) {
		$index ? $this->axisLabelPos[$index] = implode( ',', $array ) : $this->axisLabelPos[] = implode( ',', $array ) ;
	}
	
	/**
	 * Axis Range chxr
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_range
	 *
	 * @param integer $start low value
	 * @param integer $end high value
	 * @param integer $step (optional) interval
	 * @param integer $index of axis, zero-based
	 */
	function AddAxisRange( $start, $end, $step = false, $index = false ) {
		$temp = $start . ',' . $end;

		if ( $step )
			$temp .= ',' . $step;
		
		$index ? $this->axisRange[$index] = $temp : $this->axisRange[] = $temp;
	}
	
	/**
	 * Axis Label Styles chxs
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_label_styles
	 *
	 * @param string $color RRGGBB
	 * @param string $format
	 * @uses FormatString
	 * @param integer $size (optional) px
	 * @param string $align (optional) -1 | 0 | 1
	 * @param string $control (optional) l (axis), t (tick), lt (both), _ (none)
	 * @param string $tickcolor (optional) RRGGBB
	 * @param integer $index of axis, zero-based
	 */
	function AddAxisStyle( $color, $format = '', $size = false, $align = false, $control = false, $tickcolor = false, $index = false ) {
		$temp = '';
		if( !empty( $format ) )
		    $temp .= $format . ',';
		$temp .= $color;

		if ( $size ) {
			$temp .= ',' . $size;

			if ( $align ) {
				$temp .= ',' . $align;

				if ( $control ) {
					$temp .= ',' . $control;

					if ( $tickcolor )
						$temp .= ',' . $tickcolor;
				}
			}
		}
		$index ? $this->axisStyle[$index] = $temp : $this->axisStyle[] = $temp;
	}

	/**
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_label_styles
	 *
	 * @param string $pre_text (optional)
	 * @param string $type (optional) f (float), p (%), e (scientific), c<CUR> (currency)
	 * @link http://www.iso.org/iso/currency_codes_list-1
	 * @param integer $decimal_places (optional)
	 * @param boolean $trailing_zeroes (optional)
	 * @param boolean $group_sep (optional)
	 * @param string $coord (optional) x | y
	 * @param string $post_text (optional)
	 *
	 * @return string encoded format string
	 */
	function FormatString( $pre_text = '', $type = false, $decimal_places = false, $trailing_zeroes = false, $group_sep = false, $coord = false, $post_text = '') {
	    $output = 'N' . $pre_text . '*';

	    if( $type )
		$output .= $type;

	    if( $decimal_places )
		$output .= $decimal_places;

	    if( $trailing_zeroes )
		$output .= 'z';

	    if( $group_sep )
		$output .= 's';

	    if( $coord )
		$output .= $coord;

	    $output .= '*' . $post_text;

	    return $output;
	}
	
	/**
	 * Axis Tick Mark Styles chxtc
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#axis_tick_marks
	 *
	 * @param integer|array $input length or array of lengths
	 * @param integer $index of axis, zero-based
	 */
	function AddAxisTickLength( $input, $index = false ) {

	    if( is_array( $input ) )
		    $input = implode( ',', $input );

	    $index ? $this->axisTickLength[$index] = $input : $this->axisTickLength[] = $input ;
	}
	
	/**
	 * Series Colors chco [All charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_series_color
	 *
	 * @param string|array $color string or array of RRGGBB colors
	 */
	function AddChartColor( $color ) {
		is_array( $color ) ? $this->chartColor[] = implode( '|', $color ) : $this->chartColor[] = $color ;
	}
	
	/**
	 * @link http://code.google.com/intl/en/apis/chart/docs/data_formats.html#overview
	 *
	 * @param integer|array $array
	 * @param string $chartColor RRGGBB
	 * @param string $legend
	 * @param string $label
	 */
	function AddData( $array, $chartColor = false, $legend = false, $label = false) {

		if ( is_array( $array ) )
			$this->data[] = $array;
		else
			$this->data[] = array( $array );

		if ( false != $chartColor )
			$this->AddChartColor( $chartColor );

		if ( $legend != false )
			$this->Addlegend( $legend );

		if ( $label != false ) {
			if ( !is_array( $label ) )
				$label = array( $label );
			while ( count( $label ) < count( $array ) )
					$label[] = '';
			$this->AddLabel( $label );
		}
	}
	
	/**
	 * Line Markers chm=D [Bar, Candlestick, Line, Radar, Scatter]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_line_markers
	 *
	 * @param string $color RRGGBB
	 * @param integer $index of axis, zero-based
	 * @param string $point 0 (all), start:end
	 * @param integer $width px
	 * @param float $z_order (optional)
	 */
	function AddDataLineStyle( $color, $index, $point = 0, $width = 1, $z_order = false) {
		$output = 'D,' . $color . ',' . $index . ',' . $point . ',' . $width;

		if ( false !== $z_order )
		    $output .= ',' . $z_order;

		$this->chm[] = $output;
	}
	
	/**
	 * Text and Data Value Markers chm [Bar, Line, Radar, Scatter]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_data_point_labels
	 *
	 * @param string $type f (flag with text), t (text), A (annotation), N (format string)
	 * @uses FormatString
	 * @param string $content
	 * @param string $color RRGGBB
	 * @param integer $index
	 * @param string $point (optional) n.d, -1 (all), -n (every n-th), start:end:n, x:y (0.0-1.0 coords)
	 * @param integer $size px
	 * @param float $z_order (optional)
	 * @param string $placement (optional)
	 * @uses FormatPlacement
	 */
	function AddDataPointLabel( $type, $content, $color, $index, $point, $size, $z_order = 0, $placement = '') {
		$this->chm[] = $type . $content . ',' . $color . ',' . $index . ',' . $point . ',' . $size . ',' . $z_order;
	}

	/**
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_data_point_labels
	 *
	 * @param string $horizontal l (left), h (center), r (right)
	 * @param string $vertical b (bottom), v (middle), t (top)
	 * @param string $bar_rel s (base), c (center), e (top)
	 * @param integer $h_off px
	 * @param integer $v_off px
	 */
	function FormatPlacement( $horizontal = '', $vertical = '', $bar_rel = '', $h_off = '', $v_off = '') {
	       return $horizontal . $vertical . $bar_rel . ':' . $h_off . ':' . $v_off;
	}

	/**
	 * Line Fills chm [Line, Radar]
	 *
	 * http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_line_fills
	 *
	 * @param string $type B (to bottom), b (between lines)
	 * @param string $color RRGGBB
	 * @param integer $start
	 * @param integer|string $end integer, start:end (slice between points_
	 * @param integer $reserved must be 0
	 */
	function AddFillArea( $type, $color, $start = 0, $end = 0, $reserved = 0 ) {
		$this->chm[] = $type . ',' . $color . ',' . $start . ',' . $end . ',' . $reserved;
	}
		
	/**
	 * Pie Chart Labels chl
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/gallery/pie_charts.html#pie_chart_label
	 * 
	 * Google-o-meter Labels
	 * @link http://code.google.com/intl/en/apis/chart/docs/gallery/googleometer_chart.html#googleometer_label
	 * 
	 * QR code data
	 * @link http://code.google.com/intl/en/apis/chart/docs/gallery/qr_codes.html
	 * 
	 * Formula TeX data
	 * http://code.google.com/apis/chart/docs/gallery/formulas.html
	 * 
	 * @param mixed $input 
	 */
	function AddLabel( $input ) {
		is_array( $input ) ? $this->label[] = implode( '|', $input ) : $this->label[] = $input;
	}

	/**
	 * Chart Legend Text and Style chdl, chdlp [All charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_legend
	 *
	 * @param string $text
	 */
	function AddLegend( $text ) {
		$this->legend[] = $text;
	}

	/**
	 * Gradient Fills chf [Line, Bar, Google-o-meter, Radar, Scatter,Venn]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_gradient_fills
	 *
	 * @param string $type bc (background), c (chart), b<index> (bar)
	 * @param integer $angle degrees (0-90)
	 * 
	 * Takes multiple pairs of these
	 * @param string $color RRGGBB
	 * @param float $position left to right (0.0-1.0)
	 */
	function AddLinearGradient( $type, $angle, $color, $position ) {
		$args = func_get_args();
		$this->chf[] = $type . ',lg,' . $angle .','. implode( ',', array_slice( $args, 2 ) );
	}

	/**
	 * Striped fills chf [Line, Bar, Google-o-meter, Radar, Scatter, Venn]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_striped_fills
	 *
	 * @param string $type bc (background), c (chart), b<index> (bar)
	 * @param integer $angle degrees (0-90)
	 *
	 * Takes multiple pairs of these (at least two)
	 * @param string $color RRGGBB
	 * @param float $width 0.0-1.0 of chart
	 */
	function AddLinearStripes( $type, $angle, $color, $width ) {
		$args = func_get_args();
		$this->chf[] = $type . ',lg,' . $angle .','. implode( ',', array_slice( $args, 2 ) );
	}
	
	/**
	 * Line Styles chls [Line, Radar]
	 * 
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_line_styles
	 *
	 * @param integer $thickness px
	 * @param integer $segment (optional) px
	 * @param integer $blank (obptional) px
	 */
	function AddLineStyle( $thickness, $segment = false, $blank = false ) {
		$temp = $thickness;

		if( false !== $segment ) {
		    $temp .= ',' . $segment;

		    if( false !== $blank )
				$temp .= ','.$blank;
		}
		$this->lineStyle[] = $temp;
	}
	
	/**
	 * Candlestick Markers chm=F [Bar, Line]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_candlestick_markers
	 *
	 * @param integer $index
	 * @param integer $width px
	 * @param string $color (optional) RRGGBB for decreasing
	 * @param string $point (optional) n.d (index), -1 (all), -n (n-th), start:end:n
	 * $param float z_order (optional) -1.0 to 1.0
	 */
	function AddFinancialMarker( $index, $width, $color = '', $point = '', $z_order = false ) {
		$temp = 'F,' . $color . ',' . $index . ',' . $point . ',' . $width;

		if ( false !== $z_order )
			$temp .= ','.$z_order;

		$this->chm[] = $temp;
	}
	
	/**
	 * Dynamic Icon Markers chem [Bar, Line, Radar, Scatter]
	 * 
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_dynamic_markers
	 * @link http://code.google.com/intl/en/apis/chart/docs/gallery/dynamic_icons.html
	 *
	 * @param string $icon
	 * @param string $data
	 * @param string $series (optional) index
	 * @param string $point (optional) n.d (index), range,<start>,<end>,<step> , all, every,n
	 * @param float $z_order (optional) 0.0 to 1.0
	 * @param string $coord (optional) x,y
	 * @param string $offset (optional) x,y
	 */
	function AddIconMarker( $icon, $data, $series = false, $point = false, $z_order = false, $coord = false, $offset = false) {
	    $temp = array();
	    $temp[] = 's=' . $icon;
	    $temp[] = 'd=' . $data;
	    if ( $series ) $temp[] = 'ds=' . $series;
	    if ( $point ) $temp[] = 'dp=' . $point;
	    if ( $z_order ) $temp[] = 'py=' . $z_order;
	    if ( $coord ) $temp[] = 'po=' . $coord;
	    if ( $offset ) $temp[] = 'of=' . $offset;
	    $this->chem[] = 'y;' . implode( ';', $temp );
	}

	/**
	 * Range Markers chm [Bar, Candlestick, Line, Radar, Scatter]
	 * 
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_range_markers
	 * 
	 * @param string $mode r (horizontal), R (vertical)
	 * @param string $color RRGGBB
	 * @param float $start 0.0 to 1.0
	 * @param float $end 0.0 to 1.0
	 * @param integer $reserved must be 0
	 */
	function AddRangeMarker( $mode, $color, $start, $end, $reserved = 0) {
		$this->chm[] = $mode . ',' . $color . ',' . $reserved . ',' . $start . ',' . $end;
	}

	/**
	 * Shape Markers chm [Bar, Line, Radar, Scatter]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_shape_markers
	 *
	 * @param string $type a (arrow), c (cross), C (rectangle), d (diamond), E (error bar), h (horizontal at height), H (horizontal at marker), o (circle), s (square), v (vertical line), V (vertical line of length), x (X)
	 * @param string $color RRGGBB
	 * @param integer $index
	 * @param integer|string $size px, size:width
	 * @param boolean $at coordinates mode
	 * @param string $point (optional) n.d (index or height), -1 (all), -n (n-th), start:end:n, x:y (coords)
	 * @param float $z_order (optional) 0.0 to 1.0
	 * @param integer $h_off px
	 * @param integer $v_off px
	 * @param string $reserved must be empty
	 */
	function AddShapeMarker( $type, $color, $index, $size, $at = false, $point = '', $z_order = '', $h_off = '', $v_off = '', $reserved = '') {
		
		$temp = '';

		if( $at )
			$temp .= '@';

		$temp .= $type . ',' . $color . ',' . $index . ',' . $point . ',' . $size . ',' . $z_order;

		if( !empty( $h_off ) || !empty( $v_off ) )
			$temp .= ',' . $reserved . ':' . $h_off . ':' . $v_off;
		
		$this->chm[] = $temp;
	}

	/**
	 * Solid Fills chf [All Charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_solid_fills
	 *
	 * @param string $type bg (background), c (chart), a (transparent chart), b<index> (bars)
	 * @param string $color RRGGBB
	 */
	function AddSolidFill( $type, $color ) {
		$this->chf[] = $type . ',s,' . $color;
	}
	
	/**
	 *  internal, calulates extremes for data sets
	 */
	function CalcMinMax() {
		$data = array();

		foreach( $this->data as $set )
			$data = array_merge( $data, array_values( $set ) );

		$this->yMin = min( $data );
		$this->yMax = max( $data );
	}
	
	/**
	 * @return string axis data for URL
	 */
	function EncodeAxis() {
		$output = '';
		
		if ( isset( $this->axis ) )
			$output .= '&amp;chxt=' . implode( ',', $this->axis );

		if ( isset( $this->axisStyle ) )
			$output .= '&amp;chxs=' . $this->MergeIndex( $this->axisStyle );

		if ( isset( $this->axisLabel ) )
			$output .= '&amp;chxl=' . $this->MergeIndex( $this->axisLabel, ':|' );

		if ( isset( $this->axisLabelPos ) )
			$output .= '&amp;chxp=' . $this->MergeIndex( $this->axisLabelPos );

		if ( isset( $this->axisRange ) )
			$output .= '&amp;chxr=' . $this->MergeIndex( $this->axisRange );

		if ( isset( $this->axisTickLength ) )
			$output .= '&amp;chxtc=' . $this->MergeIndex( $this->axisTickLength );
		
		return $output;
	}
	
	/**
	 * @return string color data for URL
	 */
	function EncodeColor() {
		$output = '';
		
		if ( isset( $this->chartColor ) )
			$output .= '&amp;chco=' . implode( ',', $this->chartColor );

		if ( isset( $this->chf ) )
			$output .= '&amp;chf=' . implode( '|', $this->chf );
		
		return $output;
	}
	
	/**
	 * @return string series data for URL
	 */
	function EncodeData() {

		if( !isset( $this->yMin ) && !isset( $this->yMax ) )
			$this->CalcMinMax();

		if ( !isset( $this->encode ) )
			$this->SetEncode();

		$this->ScaleData();
		isset( $this->scaledData ) ? $data = &$this->scaledData : $data = &$this->data;
		$output = 'chd=';

		switch ( $this->encode ) {

			case 'text':
				$output .= 't:';
				$buffer = array();

				foreach ( $data as $set )
					$buffer[] = implode( ',', $set );

				$output .= implode( '|', $buffer );
				break;

			case 'scale':
				$output .= 't:';
				$buffer = array();

				foreach ( $data as $set )
					$buffer[] = implode( ',', $set );
				
				$output .= implode( '|', $buffer );
				$output .= '&amp;chds=' . ( $this->yMin >= 0 ? 0 : $this->yMin ). ',' . $this->yMax;
				break;

			case 'simple':
				$output .= 's:';
				$i = 0;

				foreach ( $data as $set ) {
					if ( $i > 0 )
						$output .= ',';

					foreach( $set as $num ) {

						if ( $num < 0 )
							$digit = '_';
						
						elseif( $num > 61 )
							$digit = 9;

						else
							$digit = $this->simple[$num];

						$output .= $digit;
					}
					$i++;
				}
				break;

			case 'extended':
				$output .= 'e:';
				$i = 0;

				foreach ( $data as $set ) {
					if ( $i > 0 )
						$output .= ',';
					foreach( $set as $num ) {

						if ( $num < 0 )
							$digit = '__';

						elseif( $num > 4095 )
							$digit = '..';

						else
							$digit = $this->extended[ floor( $num/64 ) ] . $this->extended[ $num%64 ];

						$output .= $digit;
					}
					$i++;
				}
				break;
						
		}
		return $output;
	}
	
	/**
	 * @return string style data for URL
	 */
	function EncodeStyle() {
		$output = '';
		
		if ( isset( $this->barStyle ) )
			$output .= $this->barStyle;

		if ( isset( $this->barZeroLine ) )
			$output .= '&amp;chp=' . implode( ',', $this->barZeroLine );

		if ( isset( $this->chartMargins ) )
			$output .= $this->chartMargins;

		if ( isset( $this->lineStyle ) )
			$output .= '&amp;chls=' . implode( '|', $this->lineStyle );

		if ( isset( $this->grid ) )
			$output .= $this->grid;

		if ( isset( $this->chm ) )
			$output .= '&amp;chm=' . implode( '|', $this->chm );
		
		return $output;
	}

	/**
	 * @return string text data for URL
	 */
	function EncodeText() {
		$output = '';
		
		if ( isset( $this->title ) )
			$output .= $this->title;

		if ( isset( $this->legend ) )
			$output .= '&amp;chdl=' . implode( '|', $this->legend );

		if ( isset( $this->legendPosition ) )
			$output .= $this->legendPosition;
		
		if ( isset( $this->label ) )
			$output .= '&amp;chl=' . implode( '|', $this->label );
		
		return $output;
	}

	/**
	 *
	 * @return string link with validation URL
	 */
	function GetDebugLink() {
	    return '<a href="' . $this->GetURL( 'validate' ) . '>&quot;'. $this->rawTitle.'&quot; debug</a>';
	}

	/**
	 * @return string <img /> tag
	 */
	function GetImg() {
		$output = "<img src='{$this->GetURL()}' width='{$this->width}' height='{$this->height}' alt='";
		$output .= isset( $this->rawTitle ) ? $this->rawTitle : 'chart' ;
		$output .= '\' />';
		return  $output;
	}

	/**
	 *
	 * @param boolean|string $type png, gif, json, validate
	 *
	 * @return string image URL
	 */
	function GetURL( $type = false ) {
		$url = $this->api;
		$url .= $this->EncodeData();
		$url .= '&amp;cht=' . $this->type;
		$url .= '&amp;chs=' . $this->width . 'x' . $this->height;
		$url .= $this->EncodeColor();
		$url .= $this->EncodeText();
		$url .= $this->EncodeAxis();
		$url .= $this->EncodeStyle();
		//$url .= '&amp;chem=' . implode( '|', $this->chem );

		if( false !== $type )
		    $url .= '&chof=' . $type;

		return $url;
	}
	
	/**
	 * Merges data series index keys with properties
	 *
	 * @param array $array
	 * @param string $keySeparator
	 * @param string $setSeparator
	 * 
	 * @return string
	 */
	function MergeIndex( $array, $keySeparator = ',', $setSeparator = '|' ) {
		$temp = array();

		foreach ( $array as $key => $set )
				$temp[] = $key . $keySeparator . $set;
		
		return implode( $setSeparator, $temp );
	}
	
	/**
	 * Bar Width and Spacing chbh
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/gallery/bar_charts.html#chbh
	 *
	 * @param integer|string $width px, a (absolute units, auto-size bars), r (relative units)
	 * @param integer|float $barSpacing (optional) px, 0.0 to 1.0 (of bar width)
	 * @param integer|float $groupSpacing (optional) px, 0.0 to 1.0 (of bar width)
	 */
	function SetBarStyle( $width, $barSpacing = false, $groupSpacing = false ) {
		$temp = '&amp;chbh=' . $width;
		
		if ( $barSpacing ) {
			$temp .= ','.$barSpacing;

			if ($groupSpacing)
				$temp .= ','.$groupSpacing;
		}
		$this->barStyle = $temp;
	}

	/**
	 * Chart Margins chma [All charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_chart_margins
	 *
	 * @param integer $left px
	 * @param integer $right px
	 * @param integer $top px
	 * @param integer $bottom px
	 * @param integer $legend_w (optional) px
	 * @param integer $legend_h (optional) px
	 */
	function SetChartMargins( $left, $right, $top, $bottom, $legend_w = false, $legend_h = false) {
		$temp = '&amp;chma=' . $left . ',' . $right . ',' . $top . ',' . $bottom;
		
		if ( $legend_w || $legend_h )
			$temp .= '|';
		    if ( $legend_w ) {
			    $temp .= $legend_w;
			    if ( $legend_h )
				    $temp .= $legend_h;
		}
		$this->chartMargins=$temp;
	}
	
	/**
	 * Sets or tries to guess fitting encode mode
	 *
	 * @param string $encode (optional) scale, simple, text, extended
	 */
	function SetEncode( $encode = false ) {

	    if ( $encode ) {
		$this->encode = $encode;
		return;
	    }

	    if( !isset( $this->yMin ) )
		$this->CalcMinMax ();

	    if ( $this->yMin < 0 )
		    $this->encode = 'scale';

	    elseif ( $this->yMax < 62 )
		    $this->encode = 'simple';

	    elseif ( $this->yMax < 101 )
		    $this->encode = 'text';

	    else
		    $this->encode = 'extended';
	}

	/**
	 * Grid Lines chg [Line, Bar, Radar, Scatter]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_grid_lines
	 *
	 * @param integer $xstep %
	 * @param integer $ystep %
	 * @param integer $segment (optional) px
	 * @param integer $blank (optional) px
	 * @param integer $xoffset (optional) chart units
	 * @param integer $yoffset (optional) chart units
	 */
	function SetGrid( $xstep, $ystep, $segment = 1, $blank = 0, $xoffset = 0, $yoffset = 0 ) {
		$this->grid = '&amp;chg=' . $xstep . ',' . $ystep . ',' . $segment . ',' . $blank . ',' . $xoffset . ',' . $yoffset;
	}
	
	/**
	 * @param integer $x px width
	 * @param integer $y px height
	 */
	function SetImageSize( $x = 0, $y = 0 ) {
		
	    if ( $x * $y > 300000 ) {
			trigger_error( 'Image size over 300000 pixels', E_USER_WARNING );
			return;
	    }
		
	    if ( $x > 1000 || $y > 1000 ) {
		trigger_error( 'Dimension over 1000 pixels', E_USER_WARNING );
		return;
	    }
			
	    if ( 0 == $x ) {
		$x = floor( 300000 / $y );
		    if ( $x > 1000 )
			$x = 1000;
	    }
	    
	    if ( 0 == $y ) {
		$y = floor( 300000 / $x );
		if ( $y > 1000 )
			$y = 1000;
	    }
	    $this->width = $x;
	    $this->height = $y;
	}
	
	/**
	 * recalculates data (if needed) so it fits nicely
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/data_formats.html
	 */
	function ScaleData() {
		unset( $this->scaledData );
		
		switch ( $this->encode ) {
			case 'text':
				$top = 100;
				$round = false;
				break;
			case 'scale':
				return;
				break;
			case 'simple':
				$top = 61;
				$round = true;
				break;
			case 'extended':
				$top = 4095;
				$round = true;
				break;
		}
		
		if ( isset( $this->scaleFactor ) )
			$top = $top * $this->scaleFactor;
		
		$this->scaledData = array();
		
		foreach( $this->data as $key => $set ) {
			$scaledSet = array();
			
			foreach( $set as $index => $value )
				if ( 0 != $this->yMax * $value )
					$scaledSet[$index] = ( $round ? round( $top / $this->yMax * $value, 0 ) : $top / $this->yMax * $value );
				else
					$scaledSet[$index] = 0;

			$this->scaledData[] = $scaledSet;
		}
	}

	/**
	 * Chart Legend Text and Style chdl, chdlp [All charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_legend
	 *
	 * @param string $pos b (bottom), bv, t (top), tv, r (right), l (left)
	 * @param string $order (optional) l, r (reverse), a (auto), "0,1,2..." (indexes)
	 */
	function SetLegendPosition( $pos = false, $order = false ) {
	    $temp = '&amp;chdlp=';

	    if( $pos )
		$temp .= $pos;

	    if ($order  )
		$temp .= '|' . $order;
	    
	    $this->legendPosition = $temp;
	}

	/**
	 * set extreme points for graph
	 *
	 * @param integer $yMin
	 * @param integer $yMax
	 * @param integer $xMin (optional)
	 * @param integer $xMax (optional)
	 */
	function SetMinMax( $yMin, $yMax, $xMin = false, $xMax = false ) {
		$this->yMin = $yMin;
		$this->yMax = $yMax;
		
		if ($xMin && $xMax) {
			$this->xMin = $xMin;
			$this->xMax = $xMax;
		}
	}

	/**
	 * Chart Title chtt, chts [All charts]
	 *
	 * @link http://code.google.com/intl/en/apis/chart/docs/chart_params.html#gcharts_chart_title
	 *
	 * @param string $title
	 * @param string $color (optional) RRGGBB
	 * @param integer $size (optional) pt
	 */
	function SetTitle( $title, $color = false, $size = false ) {
		$this->rawTitle = $title;
		$this->title = '&amp;chtt=' . str_replace( ' ', '+', $title );
		if ( $color && $size )
			$this->title .= '&amp;chts=' . $color . ',' . $size;
	}
}
?>