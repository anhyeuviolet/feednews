<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form name="EditDeclarationSite" id="EditDeclarationSite" method="post">
	<input type="hidden" name="cmd" value="1" id="cmd" />
       <button type="submit" class="btn btn-success">{LANG.save}</button>
       <a href="{BUTTON.temp}&id={ITEM.id}" style="color:#000;"><button type="button" class="btn btn-primary">{LANG.temp_site_structure}</button></a>
       <a href="{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}" style="color:#000;"><button type="button" class="btn btn-primary">{LANG.structure_list}</button></a>
       <a href="{BUTTON.add}" style="color:#000;"><button class="btn btn-primary" type="button">{LANG.add_site_structure}</button></a>

       <div class="table-responsive" style="margin-top: 10px">
          <table class="table table-striped table-bordered table-hover">
          <tr><td style="padding:2px;"><label>{LANG.sample_module}</label></td><td>
          <select class="form-control w200" name="module" id="module" onchange="window.location='{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}&id={ITEM.id}&table_name='+this.value">
          <!-- BEGIN: list_module -->
          <option {ROW.selected} value="{ROW.module_data}">{ROW.title}</option>
          <!-- END: list_module -->
          </select></td></tr><tr>
                <td style="padding:2px;"><label>{LANG.sample_name}</label></td>
                <td style="padding:2px;"><input name="name" type="text" id="name" style="width:60%;" class="form-control" value="{ITEM.name}" required="required"/></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_host}</label></td>
                <td style="padding:2px;"><input name="host" type="text" id="host" style="width:60%;" class="form-control" value="{ITEM.host}"  required="required"/></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_url}</label></td>
                <td style="padding:2px;"><input name="url" type="text" id="url" style="width:60%;" class="form-control" value="{ITEM.url}"  required="required"/></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_pattern}</label></td>
                <td style="padding:2px;"><input  required="required" name="pattern_bound" type="text" id="pattern_bound" style="width:60%;" class="form-control" value="{ITEM.pattern_bound}"  /></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_child}</label></td>
                <td style="padding:2px;"><input name="extra" type="text" id="extra" style="width:60%;" class="form-control" value="{ITEM.extra}"  required="required"/></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.news_count}</label></td>
                <td style="padding:2px;"><input name="count" type="text" id="count" style="width:60%;" class="form-control" value="{ITEM.count}"  required="required"/></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.news_category}</label></td>
                <td style="padding:2px;"><select name="catid" id="catid" class="form-control w200">
                <!-- BEGIN: list_cat -->
                <option value="{CAT.catid}" {CAT.selected}><!-- BEGIN: level -->{LEVEL}<!-- END: level -->{CAT.title}</option>
                <!-- END: list_cat -->
                </select></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.news_status}</label></td>
                <td style="padding:2px;"><select name="status" id="status" class="form-control w200">
                <option value="0" {ITEM.status0}>{LANG.no}</option>
                <option value="1" {ITEM.status1}>{LANG.yes}</option>
                </select></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.host_image}</label></td>
                <td style="padding:2px;"><select name="get_image" id="get_image" class="form-control w200">
                <option value="1" {ITEM.get_image1}>{LANG.yes}</option>
                <option value="0" {ITEM.get_image0}>{LANG.no}</option>
                </select></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_image}</label></td>
                <td style="padding:2px;"><input name="image_pattern" type="text" id="image_pattern" style="width:60%;" class="form-control" value="{ITEM.image_pattern}" /></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.replace_image_src}</label></td>
                <td style="padding:2px;">
                   <label>
                    <input name="image_content_left" type="text" id="image_content_left" style="width:35%;" class="form-control pull-left" value="{ITEM.image_content_left}" /> <span class="pull-left">==></span>
                    <input name="image_content_right" type="text" id="image_content_right" style="width:35%;" class="form-control pull-left" value="{ITEM.image_content_right}" />
                   </label>
                </td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_source}</label></td>
                <td style="padding:2px;"><select name="sourceid" id="sourceid" class="form-control w200">;
                <!-- BEGIN: list_source -->
                <option value="{SOURCE.sourceid}" {SOURCE.selected}>{SOURCE.title}</option>
                <!-- END: list_source -->
             </select></td>
             </tr>
             <tr>
                <td style="padding:2px;"><label>{LANG.sample_block}</label></td>
                <td style="padding:2px;">
                <!-- BEGIN: list_bid -->
                <input name="bid[]" type="checkbox" id="{BID.bid}" value="{BID.bid}" {BID.checked}/> <label>{BID.title}</label>
                <!-- END: list_bid -->
				</td>
				</tr>
        </table>
        </div>
        <button type="submit" class="btn btn-success">{LANG.save}</button>
		<a href="{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}" style="color:#000;"><button type="button" class="btn btn-primary">Danh sách</button></a>
</form>

<!-- END: main -->
