<!-- BEGIN: main -->
<form name="EditDeclarationSite" id="EditDeclarationSite" method="post">
	<input type="hidden" name="cmd" id="cmd" value="1" />
	<button type="submit" class="btn btn-success">{LANG.save}</button>
	<a href="{BUTTON.edit}&id={ITEM.id}" style="color:#000;"><button type="button" class="btn btn-primary">Sửa mẫu</button></a>
	<a href="{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}" style="color:#000;"><button type="button" class="btn btn-primary">Danh sách</button></a>
	<a href="{BUTTON.add}" style="color:#000;"><button type="button" class="btn btn-primary">Thêm mẫu</button></a>
	<div class="table-responsive" style="margin-top: 10px">
		<table class="table table-striped table-bordered table-hover">
            <tr bgcolor="#f2f2f2">
                <th style="padding:5px;">Trường dữ liệu</th>
                <th style="padding:5px; width:30%;">Mẫu cần lấy</th>
                <th style="padding:5px; width:30%;">Mẫu đối tượng cần xóa (cách nhau bởi dấu phẩy ",")</th>
                <th style="padding:5px; width:30%;">Chuỗi ký tự cần xóa (cách nhau bởi dấu phẩy ",")</th>
            </tr>
			
			<!-- BEGIN: field_list -->
			<tr>
				<td style="padding:2px;">{FIELD}</td>
				<td style="padding:2px;"><input name="field[{FIELD}][extra]" type="text" id="field[{FIELD}][extra]" style="width:99%;" value="{TEMP.extra}"/></td>
				<td style="padding:2px;"><input name="field[{FIELD}][element_delete]" type="text" id="field[{FIELD}][element_delete]" style="width:99%;" value="{TEMP.element_delete}"/></td>
				<td style="padding:2px;"><input name="field[{FIELD}][string_delete]" type="text" id="field[{FIELD}][string_delete]" style="width:99%;" value="{TEMP.string_delete}"/></td>
			</tr>
			<!-- END: field_list -->
			
		</table>
	</div>
</form>
<!-- BEGIN: error -->
	<div class="quote" style="width: 780px;">        
		<blockquote class="error">
			<span style="font-size:16px">Không tồn tại mẫu này</span>        
		</blockquote>
	</div>

<!-- END: error -->
<!-- END: main -->