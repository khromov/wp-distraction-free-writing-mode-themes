<?php	
	/**
	 * OOP version of php-microtemplate
	 * For more information see:
	 * http://khromov.wordpress.com/2012/08/09/micro-templates-for-rapid-web-design-prototyping-and-development-in-php/
	 **/
	class MicroTemplate
	{
		private $prefix;
		private $suppress_errors;
		
		function __construct($prefix='templates/', $suppress_errors = true)
		{
			$this->prefix = $prefix;
			$this->suppress_errors = $suppress_errors;
			
			if(!$this->short_open_tag_enabled() && !$this->suppress_errors)
				throw new Exception('PHP short tags are disabled, please set the short_open_tag directive to "On" in your php.ini');
		}
		
		/**
		 * Main templating function
		 **/
		function template($template, $v = array(), $prefix = null)
		{
			if(is_null($prefix))
				$prefix = $this->prefix;
				
			return $this->build($template, $v, $this->prefix);
		}
		
		/**
		 * Shorthand for template()
		 **/
		function t($template, $v = array(), $prefix = null)
		{
			return $this->template($template, $v, $prefix);
		}
		
		function build($template, $v, $prefix)
		{
			ob_start();

			if(file_exists($prefix.$template.'.php'))
			{
				if(($this->short_open_tag_enabled() === false))
				{
					//Short tags not enabled let's do some magic. Taken from CodeIgniter core.
					echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($prefix.$template.'.php'))));
				}
				else
				{
					include($prefix.$template.'.php');
				}
			}
			else
			{
				if(!$this->suppress_errors)
					throw new Exception('Template file '. $prefix . $template .'.php does not exist.');	
				
				//If suppress_errors = true, don't do anything
			}		

			return ob_get_clean();			
		}
		
		function short_open_tag_enabled()
		{
			return (bool)@ini_get('short_open_tag');
		}
	}
	
	/**
	 * Static shorthand version of the above class.
	 * Less flexible but easier to type:
	 * MT::t('template-name');
	 **/
	class MT
	{
		private $MicroTemplateInstance;
		
		function t($template, $v, $prefix = 'templates/', $suppress_errors = true, $rewrite_short_tags = true)
		{
			$t = new MicroTemplate($prefix, $suppress_errors, $rewrite_short_tags);
			return $t->template($template, $v, $prefix);			
		}	
	}