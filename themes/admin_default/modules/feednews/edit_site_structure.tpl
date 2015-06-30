<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form name="EditDeclarationSite" id="EditDeclarationSite" method="post">
	<input type="hidden" name="cmd" value="1" id="cmd" />
		<button type="submit" class="btn btn-success">Ghi lại</button>
		<a href=".NV_BASE_ADMINURL.index.php?.NV_NAME_VARIABLE.=.$module_name.&.NV_OP_VARIABLE.=temp_site_structure&id={ITEM.id}" style="color:#000;"><button type="button" class="btn btn-primary">Cấu trúc</button></a>
		<a href=".NV_BASE_ADMINURL.index.php?.NV_NAME_VARIABLE.=.$module_name." style="color:#000;"><button type="button" class="btn btn-primary">Danh sách</button></a>
		<a href=".NV_BASE_ADMINURL.index.php?.NV_NAME_VARIABLE.=.$module_name.&.NV_OP_VARIABLE.=add_site_structure" style="color:#000;"><button class="btn btn-primary" type="button">Thêm mẫu</button></a>
		<span style="float:right; font-weight:700;">Mục có dấu (<span class="require">*</span>) là bắt buộc</span>
		
	<div class="table-responsive" style="margin-top: 10px">
		<table class="table table-striped table-bordered table-hover">
		<tr><td style="padding:2px;"><label>Module lưu tin</label></td><td>
		<select class="form-control w200" name="module" id="module" onchange="window.location={NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}&id={ITEM.id}&table_name=+this.value">
		<option " . $select1 . " value=" . $rows[module_data] . ">" . $rows[title] . "</option>
		</select></td></tr><tr>
				<td style="padding:2px;"><label>Tên mẫu (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="name" type="text" id="name" style="width:60%;" class="form-control" value="{ITEM.name}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Host (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="host" type="text" id="host" style="width:60%;" class="form-control" value="{ITEM.host}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Url (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="url" type="text" id="url" style="width:60%;" class="form-control" value="{ITEM.url}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Mẫu bao ngoài một đối tượng (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="pattern_bound" type="text" id="pattern_bound" style="width:60%;" class="form-control" value="{ITEM.pattern_bound}"  /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Mẫu liên kết một tin (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="extra" type="text" id="extra" style="width:60%;" class="form-control" value="{ITEM.extra}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Số tin lấy (<span class="require">*</span>)</label></td>
				<td style="padding:2px;"><input name="count" type="text" id="count" style="width:60%;" class="form-control" value="{ITEM.count}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Chèn vào danh mục</label></td>
				<td style="padding:2px;"><select name="catid" id="catid" class="form-control w200">
				<!-- BEGIN: list_cat -->
				<option value="{CAT.catid}" {CAT.selected}><!-- BEGIN: level -->{LEVEL}<!-- END: level -->{CAT.title}</option>
				<!-- END: list_cat -->
				</select></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Trạng thái tin bài</label></td>
				<td style="padding:2px;"><select name="status" id="status" class="form-control w200">
				<option value="0".($item[status]==0? selected:).>Chờ duyệt</option>
				<option value="1".($item[status]==1? selected:).>Đăng bài ngay</option>
				</select></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Lấy ảnh đại diện về host</label></td>
				<td style="padding:2px;"><select name="get_image" id="get_image" class="form-control w200">
				<option value="1".($item[get_image]==1? selected:).>Có</option>
				<option value="0".($item[get_image]==0? selected:).>Không</option>
				</select></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Mẫu ảnh đại diện</label></td>
				<td style="padding:2px;"><input name="image_pattern" type="text" id="image_pattern" style="width:60%;" class="form-control" value="{ITEM.image_pattern}" /></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Thay thế đường dẫn ảnh trong nội dung</label></td>
				<td style="padding:2px;">
					<label>
					 <input name="image_content_left" type="text" id="image_content_left" style="width:35%;" class="form-control pull-left" value="{ITEM.image_content_left}" /> <span class="pull-left">==></span> 
					 <input name="image_content_right" type="text" id="image_content_right" style="width:35%;" class="form-control pull-left" value="{ITEM.image_content_right}" />
					</label>
				</td>
		   </tr>
			<tr>
				<td style="padding:2px;"><label>Nguồn tin</label></td>
				<td style="padding:2px;"><select name="sourceid" id="sourceid" class="form-control w200">;
				<option value="{SOURCE.sourceid}" {SOURCE.selected}>{SOURCE.title}</option>

			</select></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Nhóm tin</label></td>
				<td style="padding:2px;">
				<!-- BEGIN: list_bid -->
				<input name="bid[{BID.bid}]" type="checkbox" id="bid[{BID.bid}]" value="{BID.bid}" {BID.selected}/> <label>{BID.title}</label>
				<!-- END: list_bid -->
				</td>
			</tr>
		</table>
		</div>
		</form>
<!-- END: main -->
