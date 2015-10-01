<?php

class Skrollr_Color_Tools {

	/**
	* convert RGB to HSL
	*/
	static function rgb2hsl($red, $green, $blue) {
		if($red > 1 || $green > 1 || $blue > 1) {
			$red = $red / 255;
			$green = $green / 255;
			$blue = $blue / 255;
		}

		$min = min($red,$green,$blue);
		$max = max($red,$green,$blue);
		$delta = $max - $min;

		$lum = ($max + $min) / 2;

		if ($delta == 0) {
			$hue = 0;
			$sat = 0;
		} else {
			if ($lum < 0.5) {
				$sat = $delta / ($max + $min);
			} else {
				$sat = $delta / (2 - $max - $min);
			}

			$delta_r = ((($max - $red) / 6) + ($delta / 2)) / $delta;
			$delta_g = ((($max - $green) / 6) + ($delta / 2)) / $delta;
			$delta_b = ((($max - $blue) / 6) + ($delta / 2)) / $delta;

			if ($red == $max) {
				$hue = $delta_b - $delta_g;
			} elseif ($green == $max) {
				$hue = (1 / 3) + $delta_r - $delta_b;
			} elseif ($blue == $max) {
				$hue = (2 / 3) + $delta_g - $delta_r;
			}

			if ($hue < 0)	{
				$hue += 1;
			}

			if ($hue > 1)	{
				$hue -= 1;
			}
		}

		return array($hue, $sat, $lum);
	}

	/**
	* Convert hue to RBG
	*/
	static function hue2rgb($v1,$v2,$vh) {
		if ($vh < 0) {
			$vh += 1;
		}

		if ($vh > 1) {
			$vh -= 1;
		}

		if ((6 * $vh) < 1) {
			return ($v1 + ($v2 - $v1) * 6 * $vh);
		}

		if ((2 * $vh) < 1) {
			return ($v2);
		}

		if ((3 * $vh) < 2) {
			return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
		}

		return ($v1);
	}

	static function hexpadleft($v){
		return str_pad(dechex($v*255), 2, '0', STR_PAD_LEFT);
	}

	/**
	* convert HSL to RGB
	*/
	static function hsl2rgb($hue, $sat, $lum, $format='float') {
		if ($sat == 0) {
			$red = $lum;
			$green = $lum;
			$blue = $lum;
		} else {
			if ($lum < 0.5) {
				$var_2 = $lum * (1 + $sat);
			} else {
				$var_2 = ($lum + $sat) - ($sat * $lum);
			}

			$var_1 = 2 * $lum - $var_2;
			$red = Skrollr_Color_Tools::hue2rgb($var_1,$var_2,$hue + (1 / 3));
			$green = Skrollr_Color_Tools::hue2rgb($var_1,$var_2,$hue);
			$blue = Skrollr_Color_Tools::hue2rgb($var_1,$var_2,$hue - (1 / 3));
		}
	
		if($format == 'float') return array($red, $green, $blue);
		else if($format == 'dec') return array($red*255, $green*255, $blue*255);
		else if($format == 'hex') return implode('', array_map('Skrollr_Color_Tools::hexpadleft', array($red, $green, $blue)));
	}

	/**
	* convert RGB from HEX to DEC format
	*/
	static function rgbhex2dec($color) {
		return array(
			hexdec(substr($color, 1, 2)),
			hexdec(substr($color, 3, 2)),
			hexdec(substr($color, 5, 2))
		);
	}

	/**
	* Get the provided color and return a darker one with the same hue
	*/
	static function darken_color($color, $power='medium'){
		$color_dec = Skrollr_Color_Tools::rgbhex2dec( $color );
		$hsl = Skrollr_Color_Tools::rgb2hsl($color_dec[0], $color_dec[1], $color_dec[2]);
		if( $power == 'low' ){
			if( $hsl[2] < 0.5 ) $hsl[2] = $hsl[2] * 0.9;
			else $hsl[2] = $hsl[2] - 0.1;
		} else if( $power == 'medium' ){
			if( $hsl[2] < 0.5 ) $hsl[2] = $hsl[2] * 0.5;
			else $hsl[2] = $hsl[2] - 0.25;
		} else if( $power == 'strong' ){
			if( $hsl[2] < 0.5 ) $hsl[2] = $hsl[2] * 0.4;
			else $hsl[2] = $hsl[2] - 0.4;
		}
		if( $hsl[2] < 0 ) $hsl[2] = 0;
		$darker = '#'.Skrollr_Color_Tools::hsl2rgb($hsl[0], $hsl[1], $hsl[2], 'hex');
		return $darker;
	}
}

