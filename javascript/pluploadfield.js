(function($) {
	$('.plupload-widget').livequery(function(){
		var data = {};
		data.uploadURL = this.getAttribute('data-upload-url');		
		data.maxFilesize = this.getAttribute('data-max-filesize');
		
		data.filterTitle = this.getAttribute('data-filter-title');
		data.filterExtensions = this.getAttribute('data-filter-extensions');

		var filters = false;
		if (data.filterExtensions) {
			var filters = [
				{
					title: data.filterTitle,
					extensions: data.filterExtensions
				}
			];
		}
		
		$(this).pluploadQueue({
			// General settings
			runtimes : 'html5,gears,flash,silverlight,browserplus',
			url : data.uploadURL,
			max_file_size : data.maxFilesize,
		//	chunk_size : '1mb',
			chunk_size : '70kb',
			unique_names : false,

			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},

			// Specify what files to browse for
			filters : filters,

			flash_swf_url : 'plupload/javascript/thirdparty/plupload.flash.swf',
			silverlight_xap_url : 'plupload/javascript/thirdparty/plupload.silverlight.xap',
			
			
			
			init: {
				FileUploaded: function(up, file, info) {
					console.log('[FileUploaded] File:', file, "Info:", info);
				}


			}
			
		});
		
		

	});
})(jQuery);