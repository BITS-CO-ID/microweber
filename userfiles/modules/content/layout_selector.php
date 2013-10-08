<?php



//d($params);


$rand = uniqid().rand();
if(!isset($params["data-page-id"])){
	$params["data-page-id"] = PAGE_ID;
}



$live_edit_styles_check = false;
if(isset($params["live_edit_styles_check"])){
	$live_edit_styles_check = true;
}





$data = false;
if(!isset($params["layout_file"]) and isset($params["data-page-id"]) and intval($params["data-page-id"]) != 0){


 $data = mw('content')->get_by_id($params["data-page-id"]);
} else {
//	$data = $params;
}

if(!isset($params["layout_file"]) and isset($params["layout-file"])){
	$params["layout_file"] = $params["layout-file"];
}
if(!isset($params["layout_file"]) and $data == false or empty($data )){
  include('_empty_content_data.php');
}




if(isset($data['active_site_template']) and $data['active_site_template'] == ''){
 $data['active_site_template'] = ACTIVE_SITE_TEMPLATE;
}

if(isset($params["layout_file"]) and trim($params["layout_file"]) != ''){
  $data['layout_file'] = $params["layout_file"] ;
}

$inherit_from = false;
 
if(!isset($params["inherit_from"]) and isset($params["inherit-from"])){
	$params["inherit_from"] = $params["inherit-from"];
}
if((isset($params["inherit_from"]) and $params["inherit_from"] != 0) or ($data['layout_file'] == '' and (!isset($data['layout_name']) or $data['layout_name'] == '' or $data['layout_name'] == 'inherit'))){

  if(isset($params["inherit_from"]) and (trim($params["inherit_from"]) != '' or trim($params["inherit_from"]) != '0')){
//


  $inherit_from_id = mw('content')->get_by_id($params["inherit_from"]);
 // $inherit_from_id = false;
 if($inherit_from_id != false and isset($inherit_from_id['active_site_template']) and trim($inherit_from_id['active_site_template']) != 'inherit'){
$data['active_site_template']  =  $inherit_from_id['active_site_template'];
          $data['layout_file']  = $inherit_from_id['layout_file'];
 $inherit_from = $inherit_from_id;
  $data['layout_file']  = 'inherit';

 } else {
        $inh1 = mw('content')->get_inherited_parent($params["inherit_from"]);
	
        if($inh1 == false){
         $inh1 = intval($params["inherit_from"]);
       }
       if($inh1 != false){
         $inherit_from = mw('content')->get_by_id($inh1);
         if(is_array($inherit_from) and isset($inherit_from['active_site_template'])){
          $data['active_site_template']  =  $inherit_from['active_site_template'];
          $data['layout_file']  = 'inherit';
        }
      }

    }
}
}

if(!isset($params["active-site-template"]) and isset($params["site-template"])){
	$params["active-site-template"] = $params["site-template"];
}
if(isset($params["active-site-template"])){
  $data['active_site_template'] = $params["active-site-template"] ;
}

if(isset($data["id"])){
	if(!defined('ACTIVE_SITE_TEMPLATE')){
	 mw('content')->define_constants($data);
	}
 }

if(isset($data["active_site_template"]) and ($data["active_site_template"] == false or $data["active_site_template"] == NULL or trim($data["active_site_template"]) == '') and defined('ACTIVE_SITE_TEMPLATE')){
	 $data['active_site_template'] = ACTIVE_SITE_TEMPLATE;
}
 
 

$templates= mw('content')->site_templates();

$layout_options = array();
 
$layout_options  ['site_template'] = $data['active_site_template'];
$layout_options  ['no_cache'] = true;
$layouts = mw('layouts')->get_all($layout_options);

$recomended_layouts = array();
 if(isset($params['content-type'])){
	 
	 foreach($layouts  as $k => $v){
		 
		  $ctypes = array();
		  if(isset($v['content_type'])){
			  $ctypes = explode(',',$v['content_type']);
			  $ctypes = array_trim($ctypes);
			   
		  }
		 
		 if(isset($v['content_type']) 
		 and 
		 (
		 trim($v['content_type']) == trim($params['content-type'])
		 or (in_array($params['content-type'],$ctypes) == true)
		 )
		 ){
			$v['is_recomended'] = true;
			$recomended_layouts[] =  $v;
			 unset($layouts[$k]);
		 } else {
			
		 }
		 
	 }
	 
 }
if(!empty($recomended_layouts)){
	
 
	
	$layouts = array_merge($recomended_layouts,$layouts);
}
 
 
?>
<script>


safe_chars_to_str = function(str){
  if(str == undefined){
    return;
  }


  return str.replace(/\\/g,'____').replace(/\'/g,'\\\'').replace(/\"/g,'\\"').replace(/\0/g,'____');


}


mw.templatePreview = {




  set:function(){
    mw.$('.preview_frame_wrapper iframe')[0].contentWindow.scrollTo(0,0);
    mw.$('.preview_frame_wrapper').removeClass("loading");
  },
  rend:function(url){
    var holder =  mw.$('.preview_frame_container');
    var wrapper =  mw.$('.preview_frame_wrapper');
    var frame = '<iframe src="'+url+'" class="preview_frame_small" tabindex="-1" onload="mw.templatePreview.set();" frameborder="0"></iframe>';
    holder.html(frame);

  },
  next:function(){
    var index = mw.templatePreview.selector.selectedIndex;
    var next = mw.templatePreview.selector.options[index+1] !== undefined ? (index+1) : 0;
    mw.templatePreview.view(next);
  },
  prev:function(){
    var index = mw.templatePreview.selector.selectedIndex;
    var prev = mw.templatePreview.selector.options[index-1] !== undefined ? (index-1) : mw.templatePreview.selector.options.length-1;
    mw.templatePreview.view(prev);
  },
  view:function(which){

		  	//var $sel = mw.$('#active_site_layout_<?php print $rand; ?> option:selected');



        mw.templatePreview.selector.selectedIndex = which;
        mw.$("#layout_selector<?php print $rand; ?> li.active").removeClass('active');
        mw.$("#layout_selector<?php print $rand; ?> li").eq(which).addClass('active');
        $(mw.templatePreview.selector).trigger('change');
      },
      zoom:function(a){
        if(typeof a =='undefined'){
          var holder = mw.$('.preview_frame_wrapper');
          holder.toggleClass('zoom');
          holder[0].querySelector('iframe').contentWindow.scrollTo(0,0);
        }
        else if(a=='out'){
          mw.$('.preview_frame_wrapper').removeClass('zoom');
        }
        else{
         mw.$('.preview_frame_wrapper').addClass('zoom');



       }
       mw.$('.preview_frame_wrapper iframe')[0].contentWindow.scrollTo(0,0);



     },


     prepare:function(){


       var $sel = mw.$('#active_site_layout_<?php print $rand; ?> option');
       var $layout_list_rend = mw.$('#layout_selector<?php print $rand; ?>');
       var $layout_list_rend_str = '<ul>';
       if($sel.length >0){
         var indx = 0;
         $sel.each(function() {


          var  val = $(this).attr('value');
          var  selected = $(this).attr('selected');
          var  title = $(this).attr('title');
	//	mw.log(val);
		//mw.log(selected);

		 //var value=this.val();
		 $layout_list_rend_str += '<li ';
     $layout_list_rend_str += ' onclick="mw.templatePreview.view('+indx+');" ';
     if(val != undefined){
      $layout_list_rend_str += 'value="'+val+'" ';
    }
    if(val != undefined){
      $layout_list_rend_str += 'data-index="'+indx+'" ';
    }
    if(selected != undefined){
      $layout_list_rend_str += ' class="active" ';
    }
    $layout_list_rend_str += '>';
    if(title != undefined){
      $layout_list_rend_str +=   title;
    }
    $layout_list_rend_str += ' </li>';


    indx++;
  });
         $layout_list_rend_str += '</ul>';
         $layout_list_rend.html($layout_list_rend_str);
       }
	//d($sel );

},
generate:function(return_url){

  mw.$('.preview_frame_wrapper').addClass("loading");

  var template = mw.$('#active_site_template_<?php print $rand; ?> option:selected').val();
  var layout = mw.$('#active_site_layout_<?php print $rand; ?>').val();


  var is_shop = mw.$('#active_site_layout_<?php print $rand; ?> option:selected').attr('data-is-shop');
  var ctype = mw.$('#active_site_layout_<?php print $rand; ?> option:selected').attr('data-content-type');

  var inherit_from = mw.$('#active_site_layout_<?php print $rand; ?> option:selected').attr('inherit_from');



  var root = mwd.querySelector('#active_site_layout_<?php print $rand; ?>');

  var form = mw.tools.firstParentWithClass(root, 'mw_admin_edit_content_form');



  if(form != undefined && form != false){




   if(is_shop != undefined){
    if(is_shop != undefined && is_shop =='y'){
		if(form != undefined && form.querySelector('input[name="is_shop"][value="y"]') != null){
     form.querySelector('input[name="is_shop"][value="y"]').checked = true;
		}
   } else {
	   if(form != undefined && form.querySelector('input[name="is_shop"][value="y"]') != null){
     form.querySelector('input[name="is_shop"][value="n"]').checked = true;
	   }
   }
 } else {
	if(form != undefined && form.querySelector('input[name="is_shop"][value="y"]') != null){
     form.querySelector('input[name="is_shop"][value="n"]').checked = true;
	   }  
	 
 }
 
 if(ctype != undefined && ctype =='dynamic'){


 } else {
 // ctype = 'static';
}
/*mw.$("select[name='subtype']", form).val(ctype);
mw.$("input:hidden[name='subtype']", form).val(ctype);
mw.$("input:text[name='subtype']", form).val(ctype);

mw.$('input:radio[name="subtype"]', form).filter('[value="'+ctype+'"]').attr('checked', true);*/


}





if(template != undefined){
  var template = safe_chars_to_str(template);
  var template = template.replace('/','___');;

} else {

}
if(layout != undefined){
  var layout =  safe_chars_to_str(layout);
  var layout = layout.replace('/','___');
}



<?php if($data['id'] ==0){
	$iframe_start = site_url('home');
} else {
	$iframe_start = page_link($data['id']);
}

?>
var inherit_from_param = '';
if(inherit_from != undefined){
  inherit_from_param = '&inherit_template_from='+inherit_from;
}



var preview_template_param = '';
if(template != undefined){
  preview_template_param = '&preview_template='+template;
}

var preview_layout_param = '';
if(layout != undefined){
  preview_layout_param = '&preview_layout='+layout;
}

var preview_layout_content_type_param = '';
<?php if(isset($params['content-type'])): ?>
var preview_layout_content_type_param = '&content_type=<?php print $params['content-type'] ?>';

<?php endif; ?>

var iframe_url = '<?php print $iframe_start; ?>?no_editmode=true'+preview_template_param+preview_layout_param+'&content_id=<?php print  $data['id'] ?>'+inherit_from_param+preview_layout_content_type_param
 
if(return_url == undefined){
  $(window).trigger('templateChanged', iframe_url);

  mw.templatePreview.rend(iframe_url);
} else {
  return(iframe_url);
}

},
_once:false
}







$(document).ready(function() {




  mw.templatePreview.selector = mwd.getElementById('active_site_layout_<?php print $rand; ?>');

  mw.$('#active_site_template_<?php print $rand; ?>').bind("change", function(e) {
	  
	  
	  
    var parent_module = $(this).parents('.module').first();
    if(parent_module != undefined){
     parent_module.attr('data-active-site-template',$(this).val());
     mw.reload_module('<?php print $params['type']?>', function(){
       mw.templatePreview.view();
     });

   }
 });

  mw.$('#active_site_layout_<?php print $rand; ?>').bind("change", function(e) {
    mw.templatePreview.generate();
  });


  mw.templatePreview.prepare();
  <?php if(isset($params["autoload"])) : ?>
  mw.templatePreview.generate();
  <?php endif; ?>


});

</script>

<div class="layout_selector_wrap">
	<div class="vSpace"></div>
	<?php
if(defined('ACTIVE_SITE_TEMPLATE')){
 
	if( !isset($data['active_site_template']) or (isset($data['active_site_template']) and trim($data['active_site_template'])== ''and defined('ACTIVE_SITE_TEMPLATE'))){
		 $data['active_site_template'] = ACTIVE_SITE_TEMPLATE;
	}
}
 

 ?>
	<div class="mw-ui-field-holder mw-template-selector" style="padding-top: 0;<?php if( isset($params['small'])): ?>display:none;<?php endif; ?>">
		<label class="mw-ui-label">
			<?php _e("Template");   ?>
		</label>
		<div class="mw-ui-select" style="width: 200px">
			<?php if($templates != false and !empty($templates)): ?>
			<select name="active_site_template" id="active_site_template_<?php print $rand; ?>">
				<?php if( trim($data['active_site_template']) != ''): ?>
				<option value="<?php print $data['active_site_template'] ?>"      selected="selected"   ><?php print $data['active_site_template'] ?></option>
				<?php endif ?>
				<!--        <option value="default"   <?php if(('' == trim($data['active_site_template']))): ?>   selected="selected"  <?php endif; ?>>Default</option>


  <option value="inherit"   <?php if(('inherit' == trim($data['active_site_template']))): ?>   selected="selected"  <?php endif; ?>>From parent page</option>-->
				<?php foreach($templates as $item): ?>
				<?php $attrs = '';
  foreach($item as $k=>$v): ?>
				<?php $attrs .= "data-$k='{$v}'"; ?>
				<?php endforeach ?>
				<option value="<?php print $item['dir_name'] ?>"    <?php if ($item['dir_name'] == $data['active_site_template']): ?>   selected="selected"  <?php endif; ?>   <?php print $attrs; ?>  > <?php print $item['name'] ?> </option>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>
		</div>
	</div>
	<?php 
	 $is_layout_file_set = false;
	if(isset($data['layout_file']) and ('' != trim($data['layout_file']))): ?>
	<?php 
	
	$is_layout_file_set = 1; 
	if($data['layout_file'] == 'inherit'){
		$is_layout_file_set = false;
	}
	 
	$data['layout_file'] = normalize_path($data['layout_file'], false); 
	
	?>
	<?php endif; ?>
	<?php
	 
	 ?>
	<div style="display: none">
		<select name="layout_file"     id="active_site_layout_<?php print $rand; ?>"
    autocomplete="off">
			<?php if(!empty($layouts)): ?>
			<?php $i=0; $is_chosen=false; foreach($layouts as $item): ?>
			<?php $item['layout_file'] = normalize_path($item['layout_file'], false); ?>
			<option value="<?php print $item['layout_file'] ?>"  onclick="mw.templatePreview.view('<?php print $i ?>');"  
			data-index="<?php print $i ?>"  data-layout_file="<?php print $item['layout_file'] ?>"   
			
			<?php if(crc32(trim($item['layout_file'])) == crc32(trim($data['layout_file'])) ): ?> <?php $is_chosen=1; ?>  selected="selected"  <?php  endif; ?>
			 <?php if(isset($item['is_default']) and $item['is_default'] != false ): ?>   
			   data-is-default="<?php print $item['is_default'] ?>" <?php if($is_layout_file_set == false and $is_chosen==false): ?>   selected="selected" <?php $is_chosen=1; ?>  <?php endif; ?> <?php endif; ?>  
			   <?php if(isset($item['is_recomended']) and $item['is_recomended'] != false ): ?>   data-is-is_recomended="<?php print $item['is_recomended'] ?>" <?php if($is_layout_file_set == false and $is_chosen==false): ?>   selected="selected" <?php $is_chosen=1; ?> <?php endif; ?>  <?php endif; ?>
			 <?php if(isset($item['content_type']) ): ?>   data-content-type="<?php print $item['content_type'] ?>" <?php else: ?> data-content-type="static"  <?php endif; ?> 
			 
			 <?php if(isset($item['is_shop']) ): ?>   data-is-shop="<?php print $item['is_shop'] ?>"  <?php endif; ?> 
			  <?php if(isset($item['name']) ): ?>   title="<?php print $item['name'] ?>"  <?php endif; ?>
			   >
			<?php   print $item['name'] ?>
			</option>
			<?php $i++; endforeach; ?>
			<?php endif; ?>
			<?php if(!isset($params['content-type'])): ?>
			<option title="Inherit" <?php if(isset($inherit_from) and isset($inherit_from['id'])): ?>   inherit_from="<?php print $inherit_from['id'] ?>"  <?php endif; ?> value="inherit"  <?php if($is_chosen==false and trim($data['layout_file']) == '' or trim($data['layout_file']) == 'inherit'): ?>   selected="selected"  <?php endif; ?>>
			
			Inherit from parent
			
			</option>
			<?php endif; ?>
		</select>
	</div>
	<div class="left">
		<div class="preview_frame_wrapper loading left">
			<?php if( !isset($params['edit_page_id'])): ?>
			<div class="preview_frame_ctrls">
				<?php /* <span class="zoom" title="<?php _e('Zoom in/out'); ?>" onclick="mw.templatePreview.zoomIn();"></span> */ ?>
				<span class="prev" title="<?php _e('Previous layout'); ?>" onclick="mw.templatePreview.prev();"></span> <span class="next" title="<?php _e('Next layout'); ?>" onclick="mw.templatePreview.next();"></span> <span class="close" title="<?php _e('Close'); ?>" onclick="mw.templatePreview.zoom();mw.$('.mw_overlay').remove();"></span> </div>
			<?php endif; ?>
			<div class="preview_frame_container"></div>
			<?php if( !isset($params['edit_page_id'])): ?>
			<div class="mw-overlay" onclick="mw.templatePreview.zoom();">&nbsp;</div>
			<?php else: ?>
			<div class="mw-overlay mw-overlay-quick-link" onclick="mw.url.windowHashParam('action', 'editpage:<?php print $params["edit_page_id"]; ?>')">
				<div id="preview-edit-links"> <a class="mw-ui-btn" href="#action=editpage:<?php print $params["edit_page_id"]; ?>"> <span class="ico ieditpage"></span><span>
					<?php _e("Edit Page"); ?>
					</span> </a> <a class="mw-ui-btn mw-ui-btn-blue" target="_top" href="<?php print mw('content')->link($params["edit_page_id"]); ?>/editmode:y"><span class="ico ilive"></span>
					<?php _e("Go Live Edit"); ?>
					</a> </div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="layouts_box_holder <?php if( isset($params['small'])): ?> semi_hidden  <?php endif; ?>" style="margin-top: 10px;">
		<label class="mw-ui-label">
			<?php _e("Choose Page Layout"); ?>
		</label>
		<div class="layouts_box_container">
			<div class="layouts_box" id="layout_selector<?php print $rand; ?>">
				<?php
	  /*<ul>
        <li value="inherit"  onclick="mw.templatePreview.view(0);"  <?php if(('' == trim($data['layout_file']))): ?>   selected="selected"  <?php endif; ?>>None</li>
        <?php if(!empty($layouts)): ?>
        <?php $i=0; foreach($layouts as $item): ?>
        <?php $i++; ?>
        <li value="<?php print $item['layout_file'] ?>"  onclick="mw.templatePreview.view(<?php print $i; ?>);"   title="<?php print $item['layout_file'] ?>"   <?php if(($item['layout_file'] == $data['layout_file']) ): ?>   selected="selected"   class="active"  <?php endif; ?>   > <?php print $item['name'] ?> </li>
        <?php endforeach; ?>
        <?php endif; ?>
        </ul>*/

        ?>
			</div>
		</div>
	</div>
	<div class="mw_clear">&nbsp;</div>
	<?php if($live_edit_styles_check != false): ?>
	<module type="content/layout_selector_custom_css" id="layout_custom_css_clean<?php print $rand; ?>" template="<?php print $data['active_site_template'] ?>" />
	<?php endif; ?>
	<div class="vSpace">&nbsp;</div>
</div>