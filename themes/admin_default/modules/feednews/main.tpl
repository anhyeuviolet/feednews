<!-- BEGIN: main -->
<!-- BEGIN: complete -->
<div class="alert alert-info"><strong>{LANG.notice}</strong>: đã lấy được <strong>{TOTAL}</strong> tin!</div>
<div class="clear"></div>
<!-- END: complete -->

<!-- BEGIN: error -->
<div class="alert alert-danger"><strong>{LANG.error}</strong>: {ERROR}</div>
<div class="clear"></div>
<!-- END: error -->

<form name="feedForm" method="post">
<input type="hidden" name="cmd" value="feed" id="cmd" />
	<div>
		<button type="button" class="btn btn-success" onclick="feedNews();">{LANG.get_news}</button>
		<a href="{BUTTON.add}" style="color:#000;"><button type="button" class="btn btn-primary">{LANG.add_site_structure}</button></a>
		<button type="button" class="btn btn-danger" onclick="deletePattern();">{LANG.remove_site_structure}</button>
        <div class="table-responsive" style="margin-top: 10px">
			<table class="table table-striped table-bordered table-hover">
        	<tr bgcolor="#f2f2f2">
				<th style="padding:5px;">#</th>
            	<th style="padding:5px;">Mẫu lấy tin</th>
            	<th style="padding:5px;">Chèn vào danh mục</th>
            	<th style="padding:5px;">Trạng thái bài</th>
				<th style="padding:5px;" nowrap width="1%" align="center">Hành động</th>
            </tr>
			<!-- BEGIN: list_pattern -->
			<tr>
				<td style="padding:5px;" width="1%" align="center"><input name="temps[{ROW.id}]" value="{ROW.id}" class="selected_ids temps-item" type="checkbox" id="temps[{ROW.id}]" /></td>
                <td style="padding:5px;"><label for="temps[{ROW.id}]"><strong>{ROW.name} </strong></label>[ <a href="{ROW.url}" target="_blank" title="{ROW.url}">link</a> ]</td>
                <td style="padding:5px;">{ROW.cat_title} - {ROW.table_name}</td>
                <td style="padding:5px;">{ROW.status}</td>
				<td style="padding:5px;" align="center" nowrap width="1%">
					<a href="{BUTTON.edit}&id={ROW.id}">{LANG.edit_site_structure}</a> |
					<a href="{BUTTON.temp}&id={ROW.id}">Cấu trúc</a> |
					<a href="{BUTTON.copy}&id={ROW.id}">{LANG.copy_site_structure}</a>
				</td>
            	</tr>
			<!-- END: list_pattern -->
        </table>
        </div>
		<button type="button" class="btn btn-success" onclick="feedNews();">{LANG.get_news}</button>
		<a href="{BUTTON.add}" style="color:#000;"><button type="button" class="btn btn-primary">{LANG.add_site_structure}</button></a>
		<button type="button" class="btn btn-danger" onclick="deletePattern();">{LANG.remove_site_structure}</button>
	</div>
</form>
<!-- END: main -->