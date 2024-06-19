var initielized = false;
var editorPHP = null;
var editorJSP = null;
var editorSQL = null;

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};
function upperCamelize(input)
{
	return input.replaceAll("_", " ").capitalize().prettify().replaceAll(" ", "").trim();
}
function initCodeMirror()
{
	if(!initielized)
	{
		$('.code-area').css({'display':'block'});

		editorPHP = CodeMirror.fromTextArea(document.getElementById("text-code-php"), {
			lineNumbers: true,
			matchBrackets: true,
			mode: "application/x-httpd-php"
		});
		editorJSP = CodeMirror.fromTextArea(document.getElementById("text-code-jsp"), {
			lineNumbers: true,
			matchBrackets: true,
			mode: "text/x-csrc"
		});
		editorSQL = CodeMirror.fromTextArea(document.getElementById("sql-from-server"), {
			mode: "text/x-pgsql",
			indentWithTabs: true,
			smartIndent: true,
			lineNumbers: true,
			matchBrackets : true
		});
		initielized = true;
	}
}
function saveState(frm1, frm2)
{
	var arr = frm1.serializeArray();
	var value = JSON.stringify(arr);
	var dbInfo = databaseInfo(frm2);
	var host = dbInfo.host.val();
	var port = dbInfo.port.val();
	var database = dbInfo.database.val();
	var table = dbInfo.table.val();

	var key = host+"_"+port+"_"+database+"_"+table;
	window.localStorage.setItem(key, value);
}

function databaseInfo(frm2)
{
	var host = $(frm2).find('#host');
	var port = $(frm2).find('#port');
	var database = $(frm2).find('#database');
	var table = $(frm2).find('#table');
	return {host:host, port:port, database:database, table:table};
}

function hasKey(defdata, key)
{
	var len = defdata.length;
	var i;
	for(i = 0; i<len; i++)
	{
		if(defdata[i].value == key)
		{
			return true;
		}
	}
	return false;
}
function loadState(defdata, frm1, frm2)
{

	var i;
	frm2.find("tbody tr").each(function(index, e){
		var tr = $(this);
		var field = tr.find('input[type="hidden"][name="field"]').val();
		if(hasKey(defdata, field))
		{
			$(frm2).find('input[name$="'+field+'"]').each(function(index2, e2){
				$(this)[0].checked = false;
			});
		}
	});
	
	
	for(i in defdata)
	{
		var obj = $(frm2).find(':input[name='+defdata[i]['name']+']');
		if(obj.length)
		{
			var val = defdata[i]['value'];
			var name = defdata[i]['name'];
			var tagName = obj.prop("tagName").toString().toLowerCase();
			var type = obj.attr('type');
			
			if(type == 'radio')
			{
				$(frm2).find('[name="'+name+'"][value="'+val+'"]')[0].checked = true;
			}
			else if(type == 'checkbox' && val != null && val != 0 && val != "0")
			{
				$(frm2).find('[name="'+name+'"]')[0].checked = true;
			}
			else if(tagName == 'select')
			{
				obj.val(defdata[i]['value']);
			}
		}
	}
}

function getReferenceResource()
{
	return `
	<form action="">
	<label for="reference_type_entity"><input type="radio" class="reference_type" name="reference_type" id="reference_type_entity" value="entity" checked> Entity</label>
	<label for="reference_type_map"><input type="radio" class="reference_type" name="reference_type" id="reference_type_map" value="map"> Map</label>
	<label for="reference_type_yesno"><input type="radio" class="reference_type" name="reference_type" id="reference_type_yesno" value="yesno"> Yes/No</label>
	<label for="reference_type_truefalse"><input type="radio" class="reference_type" name="reference_type" id="reference_type_truefalse" value="truefalse"> True/False</label>
	<label for="reference_type_onezero"><input type="radio" class="reference_type" name="reference_type" id="reference_type_onezero" value="onezero"> 1/0</label>
	<div class="reference-container">
	  <div class="reference-section entity-section">
		<h4>Entity</h4>
		<table data-name="entity" class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tbody>
			<tr>
			  <td>Entity Name</td>
			  <td><input class="form-control rd-entity-name" type="text"></td>
			</tr>
			<tr>
			  <td>Table Name</td>
			  <td><input class="form-control rd-table-name" type="text"></td>
			</tr>
			<tr>
			  <td>Primary Key</td>
			  <td><input class="form-control rd-primary-key" type="text"></td>
			</tr>
			<tr>
			  <td>Value Column</td>
			  <td><input class="form-control rd-value-column" type="text"></td>
			</tr>
			<tr class="display-reference">
			  <td>Reference Object Name</td>
			  <td><input class="form-control rd-reference-object-name" type="text"></td>
			</tr>
			<tr class="display-reference">
			  <td>Reference Property Name</td>
			  <td><input class="form-control rd-reference-property-name" type="text"></td>
			</tr>
		  </tbody>
		</table>
		<h4>Specfification</h4>
		<p>Just leave it blank if it doesn't exist. Click Rem button to remove value.</p>
		<table data-name="specification" class="table table-reference" data-empty-on-remove="true">
		  <thead>
			<tr>
			  <td width="45%">Column Name</td>
			  <td>Value</td>
			  <td width="62">Rem</td>
			</tr>
		  </thead>
			  <tbody>
			  <tr>
				<td><input class="form-control rd-column-name" type="text" value=""></td>
				<td><input class="form-control rd-value" type="text" value=""></td>
				<td><button type="button" class="btn btn-danger btn-remove-row">Rem</button></td>
			  </tr>
			</tbody>
			<tfoot>
			  <tr>
				<td colspan="3">
				  <button type="button" class="btn btn-primary btn-add-row">Add Row</button>
				</td>
			  </tr>
			</tfoot>
		</table>
		<h4>Sortable</h4>
		<p>Use at least one column to sort.</p>
		<table data-name="sortable" class="table table-reference">
		  <thead>
			<tr>
			  <td width="65%">Column</td>
			  <td>Value</td>
			  <td width="62">Rem</td>
			</tr>
		  </thead>
			  <tbody>
			  <tr>
				<td><input class="form-control rd-column-name" type="text" value=""></td>
				<td><select class="form-control rd-order-type">
				  <option value="PicoSort::ORDER_TYPE_ASC">ASC</option>
				  <option value="PicoSort::ORDER_TYPE_DESC">DESC</option>
				</select></td>
				<td><button type="button" class="btn btn-danger btn-remove-row">Rem</button></td>
			  </tr>
			</tbody>
			<tfoot>
			  <tr>
				<td colspan="3"><button type="button" class="btn btn-primary btn-add-row">Add Row</button></td>
			  </tr>
			</tfoot>
		</table>
		<h4>Additional Output</h4>
		<p>Just leave it blank if it doesn't exist. Click Rem button to remove value.</p>
		<table data-name="additional-output" class="table table-reference" data-empty-on-remove="true">
		  <thead>
			<tr>
			  <td>Column</td>
			  <td width="62">Rem</td>
			</tr>
		  </thead>
			  <tbody>
			  <tr>
				<td><input class="form-control rd-column-name" type="text" value=""></td>
				<td><button type="button" class="btn btn-danger btn-remove-row">Rem</button></td>
			  </tr>
			</tbody>
			<tfoot>
			  <tr>
				<td colspan="3"><button type="button" class="btn btn-primary btn-add-row">Add Row</button></td>
			  </tr>
			</tfoot>
		</table>
	  </div>
	  <div class="reference-section map-section">
		<h4>Map</h4>
		<table data-name="map" class="table table-reference" data-offset="2">
		  <thead>
			<tr>
			  <td>Value</td>
			  <td>Label</td>
			  <td><input class="form-control map-key" type="text" value="" placeholder="Additional attribute name"></td>
			  <td>Def</td>
			  <td>Rem</td>
			</tr>
		  </thead>
			  <tbody>
			  <tr>
				<td><input class="form-control rd-value" type="text" value=""></td>
				<td><input class="form-control rd-label" type="text" value=""></td>
				<td><input class="form-control map-value" type="text" value="" placeholder="Additional attribute value"></td>
				<td><input type="checkbox" class="rd-selected"></td>
				<td><button type="button" class="btn btn-danger btn-remove-row">Rem</button></td>
			  </tr>
			</tbody>
			<tfoot>
			  <tr>
				<td colspan="5">
				  <button type="button" class="btn btn-primary btn-add-row">Add Row</button>
				  <button type="button" class="btn btn-primary btn-add-column">Add Column</button>
				  <button type="button" class="btn btn-primary btn-remove-last-column">Remove Last Column</button>
				</td>
			  </tr>
			</tfoot>
		</table>
	  </div>
	</div>
  </form>
  `;
}


$(document).ready(function(){
	$(document).on('click', '#load_table', function(e2){
		loadTable();
	});
	$(document).on('click', '#load_column', function(e2){
		let tableName = $('[name="source_table"]').val();
		let selector = $('table.main-table tbody');
		loadColumn(tableName, selector);
	});
	$(document).on('change', 'select[name="source_table"]', function(e2){
		let masterTableName = $(this).val();
		let moduleFileName = masterTableName+'.php';
		let moduleName = masterTableName;		
		let masterPrimaryKeyName = $(this).find('option:selected').attr('data-primary-key') || '';
		updateTableName(moduleFileName, moduleName, masterTableName, masterPrimaryKeyName)	
	});
	$(document).on('click', '#save_application_config', function(e2){
		let frm = $(this).closest('form');
		let inputs = $(this).closest('form').serializeArray();
		let current_application = frm.find('[name="current_application"]').val();

		let dataToPost = {
			current_application:current_application,
			database:{},
			sessions:{},
			entity_info:{}
		};

		for(let i in inputs)
		{
			let name = inputs[i].name;
			if(name.indexOf('database_') !== -1)
			{
				dataToPost.database[name.substring(9)] = inputs[i].value;;
			}
			if(name.indexOf('sessions_') !== -1)
			{
				dataToPost.sessions[name.substring(9)] = inputs[i].value;;
			}
			if(name.indexOf('entity_info_') !== -1)
			{
				dataToPost.entity_info[name.substring(12)] = inputs[i].value;;
			}
		}
		
		updateCurrentApplivation(dataToPost);
		
	});

	$(document).on('click', '#generate-script', function(e2){
		generateScript($('.main-table tbody'));
	});

	$(document).on('click', '#switch-application', function(e2){
		switchApplication($('#current_application').val());
	});

	$(document).on('change', '.input-field-filter', function(e2){
		let checked = $(this)[0].checked;
		let value = $(this).attr('value');
		if(checked)
		{
			let parentObj = $(this).closest('tr'); 
			parentObj.find('.input-field-filter[value!="'+value+'"]')[0].checked = false;
			prepareReferenceFilter(value, $(this));
		}
	});

	$(document).on('change', '.input-element-type', function(e2){
		let checkedValue = $(this).attr('value');
		prepareReferenceData(checkedValue, $(this));
	});

	$(document).on('click', '.reference-button-data', function(e2){
		$('#modal-create-reference-data').find('.modal-title').text('Create Data Reference');
		$('#modal-create-reference-data').attr('data-reference-type', 'data');
		let parentTd = $(this).closest('td'); 
		let parentTr = $(this).closest('tr'); 
		let fieldName = parentTr.attr('data-field-name');
		let key = $(this).siblings('input').attr('name');		

		$('#modal-create-reference-data').attr('data-input-name', key);
		$('#modal-create-reference-data').attr('data-field-name', fieldName);		
		$('#modal-create-reference-data').find('.modal-body').empty();
		$('#modal-create-reference-data').find('.modal-body').append(getReferenceResource());
		
		let value = $('[name="'+key+'"]').val();
		if(value.length < 60)
		{
			console.log('load file')
			loadReference(fieldName, key, function(obj){
				if(obj != null)
				{
					deserializeForm(obj);
				}
			});
		}
		if(value != '')
		{
			let obj = JSON.parse(value);
			deserializeForm(obj);
		}
		$('#modal-create-reference-data').modal('show');
	});

	$(document).on('click', '.reference-button-filter', function(e2){
		$('#modal-create-reference-data').find('.modal-title').text('Create Filter Reference');
		$('#modal-create-reference-data').attr('data-reference-type', 'filter');

		let parentTd = $(this).closest('td'); 
		let parentTr = $(this).closest('tr'); 
		let fieldName = parentTr.attr('data-field-name');
		let key = $(this).siblings('input').attr('name');
		
		$('#modal-create-reference-data').attr('data-input-name', key);
		$('#modal-create-reference-data').attr('data-field-name', fieldName);		
		$('#modal-create-reference-data').find('.modal-body').empty();
		$('#modal-create-reference-data').find('.modal-body').append(getReferenceResource());
		
		let value = $('[name="'+key+'"]').val().trim();
		if(value.length < 60)
		{
			loadReference(fieldName, key, function(obj){
				if(obj != null)
				{
					deserializeForm(obj);
				}
			});
		}
		if(value != '')
		{
			let obj = JSON.parse(value);
			deserializeForm(obj);
		}

		$('#modal-create-reference-data').modal('show');
	});

	$(document).on('click', '#apply-reference', function(e2){
		let key = $('#modal-create-reference-data').attr('data-input-name');
		let value = JSON.stringify(serializeForm());
		$('[name="'+key+'"]').val(value);
		$('#modal-create-reference-data').modal('hide');
	});
	$(document).on('click', '#save-to-cache', function(e2){
		let fieldName = $('#modal-create-reference-data').attr('data-field-name');
		let key = $('#modal-create-reference-data').attr('data-input-name');
		let value = JSON.stringify(serializeForm());	
		saveReference(fieldName, key, value);
	});

	$(document).on('click', '#load-from-cache', function(e2){
		let fieldName = $('#modal-create-reference-data').attr('data-field-name');
		let key = $('#modal-create-reference-data').attr('data-input-name');
		loadReference(fieldName, key, function(obj){
			if(obj != null)
			{
				deserializeForm(obj);
			}
		});
	});

	$(document).on('click', '.reference_type', function(e2){
	   let referenceType = $(this).val();
	   selectReferenceType({type:referenceType});
	});

	$(document).on('click', '.btn-add-column', function(e2){
	  let table = $(this).closest('table');
	  addColumn(table);
	});

	$(document).on('click', '.btn-remove-last-column', function(e2){
	  let table = $(this).closest('table');
	  removeLastColumn(table);
	});

	$(document).on('click', '.btn-add-row', function(e2){
	  let table = $(this).closest('table');
	  addRow(table);
	});

	$(document).on('click', '.btn-remove-row', function(e2){
	  let nrow = $(this).closest('tbody').find('tr').length;
	  if(nrow > 1)
	  {
		$(this).closest('tr').remove();
	  }
	  else if(nrow == 1 && $(this).closest('table').attr('data-empty-on-remove') == 'true')
	  {
		$(this).closest('tr').find(':input').each(function(e3){
		  $(this).val('');
		});
	  }
	});

	
	$(document).on('change', '.map-section input[type="checkbox"]', function(e){
	  if($(this)[0].checked)
	  {
		$(this).closest('tr').siblings().each(function(){
		  $(this).find('input[type="checkbox"]')[0].checked = false;
		});
	  }
	});

	$(document).on('change', '.entity-checkbox', function(e){
		let ents = getEntitySelection();
		getEntityQuery(ents);
	});

	loadTable();
	updateEntity();
});

function getEntitySelection()
{
	let ents = [];
	$('.entity-checkbox').each(function(){
		if($(this)[0].checked)
		{
			ents.push($(this).val());
		}
	});
	return ents;
}

function getEntityQuery(entity)
{
	$.ajax({
		type:'POST',
		url:'lib.ajax/entity-query.php',
		data:{entity:entity},
		dataType:'html',
		success: function(data){
			$('.entity-query').empty().append(data)
		}
	})
}

function updateEntity()
{
	$.ajax({
		type:'GET',
		url:'lib.ajax/list-entity.php',
		dataType:'html',
		success: function(data){
			$('.entity-list').empty().append(data);
			let ents = getEntitySelection();
			getEntityQuery(ents);
		}
	})
}


function saveReference(fieldName, key, value)
{
	$.ajax({
		type:'POST',
		url:'lib.ajax/save-reference.php',
		data:{fieldName:fieldName, key:key, value:value},
		dataType:'json',
		success: function(data){
			console.log(data)
		}
	})
}
function loadReference(fieldName, key, clbk)
{
	$.ajax({
		type:'POST',
		url:'lib.ajax/load-reference.php',
		data:{fieldName:fieldName, key:key},
		dataType:'json',
		success: function(data){
			clbk(data)
		}
	})
}

function updateTableName(moduleFileName, moduleName, masterTableName, masterPrimaryKeyName)
{
	moduleFileName = moduleFileName.replaceAll('_', '-');
	moduleName = moduleName.replaceAll('_', '-');
	
	let masterEntityName = upperCamelize(masterTableName);
	let approvalEntityName = masterEntityName+'Apv';
	let trashEntityName = masterEntityName+'Trash';
	
	let approvalTableName = masterTableName + '_apv';
	let approvalPrimaryKeyName = approvalTableName + '_id';
	let trashTableName = masterTableName + '_trash';
	let trashPrimaryKeyName = trashTableName + '_id';
	$(this).attr('data-value', masterTableName);

	
	$('[name="primary_key_master"]').val(masterPrimaryKeyName);
	$('[name="entity_master_name"]').val(masterEntityName);
	$('[name="entity_approval_name"]').val(approvalEntityName);
	$('[name="entity_trash_name"]').val(trashEntityName);
	$('[name="table_approval_name"]').val(approvalTableName);
	$('[name="primary_key_approval"]').val(approvalPrimaryKeyName);
	$('[name="table_trash_name"]').val(trashTableName);
	$('[name="primary_key_trash"]').val(trashPrimaryKeyName);
	$('[name="module_file"]').val(moduleFileName);
	$('[name="module_name"]').val(moduleName);
}


function prepareReferenceData(checkedValue, ctrl)
{
	let tr = ctrl.closest('tr');
	if(checkedValue == 'select')
	{
		tr.find('.reference-button-data').css('display', 'inline');
	}
	else
	{
		tr.find('.reference-button-data').css('display', 'none');
	}
}
function prepareReferenceFilter(checkedValue, ctrl)
{
	let tr = ctrl.closest('tr');
	if(checkedValue == 'select')
	{
		tr.find('.reference-button-filter').css('display', 'inline');
	}
	else
	{
		tr.find('.reference-button-filter').css('display', 'none');
	}
}

function switchApplication(currentApplication)
{
	window.location = './?current_application='+currentApplication;
}

function generateScript(selector)
{
	let fields = [];
	$(selector).find('tr').each(function(e){
		let fieldName = $(this).attr('data-field-name');
		let fieldLabel = $(this).find('input.input-field-name').val();
		let includeInsert = $(this).find('input.include_insert')[0].checked;
		let includeEdit = $(this).find('input.include_edit')[0].checked;
		let includeDetail = $(this).find('input.include_detail')[0].checked;
		let includeList = $(this).find('input.include_list')[0].checked;
		let isKey = $(this).find('input.include_key')[0].checked;
		let isInputRequired = $(this).find('input.include_required')[0].checked;
		let elementType = $(this).find('input.input-element-type:checked').val();
		let filterElementType = $(this).find('input.input-field-filter:checked').length > 0 ? $(this).find('input.input-field-filter:checked').val() : null;
		let dataType = $(this).find('select.input-field-data-type').val();
		let inputFilter = $(this).find('select.input-data-filter').val();

		let referenceData = parseJsonData($(this).find('input.reference-data').val());
		let referenceFilter = parseJsonData($(this).find('input.reference-filter').val());

		let field = {
			fieldName: fieldName,
			fieldLabel: fieldLabel,
			includeInsert: includeInsert,
			includeEdit: includeEdit,
			includeDetail: includeDetail,
			includeList: includeList,
			isKey: isKey,
			isInputRequired: isInputRequired,
			elementType: elementType,
			filterElementType: filterElementType,
			dataType: dataType,
			inputFilter: inputFilter,

			referenceData: referenceData,
			referenceFilter: referenceFilter
		};
    	fields.push(field);
	});

	let requireApproval = $('#with_approval')[0].checked && true;
	let withTrash = $('#with_trash')[0].checked && true;
	let manualSortOrder = $('#manualsortorder')[0].checked && true;
	let activateDeactivate = $('#activate_deactivate')[0].checked && true;
	let withApprovalNote = $('#with_approval_note')[0].checked && true;
	let entity = {
		mainEntity:{
			entityName: $('[name="entity_master_name"]').val(),
			tableName: $('[name="source_table"]').val(),
			primaryKey: $('[name="primary_key_master"]').val()
		},
		approvalRequired: requireApproval,
		trashRequired: withTrash
	}

	if(requireApproval)
	{
		entity.approvalEntity = {
			entityName: $('[name="entity_approval_name"]').val(),
            tableName: $('[name="table_approval_name"]').val(),
            primaryKey: $('[name="primary_key_approval"]').val()
		}
	}

	if(withTrash)
	{
		entity.trashEntity = {
			entityName: $('[name="entity_trash_name"]').val(),
            tableName: $('[name="table_trash_name"]').val(),
            primaryKey: $('[name="primary_key_trash"]').val()
		}
	}
	
	let features = {
		activateDeactivate: activateDeactivate,
		sortOrder: manualSortOrder,
		approvalRequired: requireApproval,
		approvalNote: withApprovalNote,
		trashRequired: withTrash
	};

	let dataToPost = {
		entity: entity,
		fields: fields,
		features: features,
		module_name: $('[name="module_name"]').val(),
		module_file: $('[name="module_file"]').val()
	};
	generateAllCode(dataToPost);
}

function parseJsonData(text)
{
	if (typeof text !== "string") {
		return null;
	}
	try {
		var json = JSON.parse(text);
		if(typeof json === 'object')
		{
			return json;
		}
	}
	catch (error) {
		// do nothing
	}
	return null;
}

function generateAllCode(dataToPost)
{
	$.ajax({
		type:'post', 
		url: 'lib.ajax/script-generator.php',
		dataType:'json',
		data:dataToPost,
		success: function(data)
		{
			loadTable();
		}
	});
}

function updateCurrentApplivation(dataToPost)
{
	$.ajax({
		type:'post', 
		url: 'lib.ajax/update-current-application.php',
		dataType:'json',
		data:dataToPost,
		success: function(data)
		{
			$('select[name="source_table"]').empty();
			for(let i in data)
			{
				$('select[name="source_table"]')[0].append(new Option(data[i].table_name, data[i].table_name));
			}
			let val = $('select[name="source_table"]').attr('data-value');
			if(val != null && val != '')
			{
				$('select[name="source_table"]').val(val);
			}
		}
	});
}

function loadTable()
{
	$.ajax({
		type:'post', 
		url: 'lib.ajax/list-table.php',
		dataType:'json',
		success: function(data)
		{
			$('select[name="source_table"]').empty();
			$('select[name="source_table"]')[0].append(new Option('- Select Table -', ''));
			for(let i in data)
			{
				if(data.hasOwnProperty(i))
				{
					$('select[name="source_table"]')[0].append(new Option(data[i].table_name, data[i].table_name));
				}
			}
			$('select[name="source_table"]').find('option').each(function(e3){
				let val = $(this).attr('value') || '';
				if(val != '' && typeof data[val] != 'undefined' && typeof data[val].primary_key != 'undefined' && typeof data[val].primary_key[0] != 'undefined')
				{
					$(this).attr('data-primary-key', data[val].primary_key[0]);
				}
			});
			let val = $('select[name="source_table"]').attr('data-value');
			if(val != null && val != '')
			{
				$('select[name="source_table"]').val(val);
			}
		}
	});
}
function loadColumn(tableName, selector)
{
	$.ajax({
		type:'post', 
		url: 'lib.ajax/list-column.php',
		data: {table_name: tableName},
		dataType:'json',
		success: function(answer)
		{
			$(selector).empty();
			var data = answer.fields;
			var i;
			var field, args;
			var DOMHTML;
			var so = false;
			let skipedOnInsertEdit = getSkipedCol();
			
			for(i in data)
			{
				field = data[i].column_name;
				if(field == 'sort_order')
				{
					so = true;
				}
				args = {type:data[i].data_type};
				DOMHTML = generateRow(field, args, skipedOnInsertEdit);
				$(selector).append(DOMHTML);
			}
			if(so)
			{
				$('#manualsortorder').parent().css({'display':'inline'});
			}
			else
			{
				$('#manualsortorder').parent().css({'display':'none'});
				$('#manualsortorder')[0].checked = false;
			}
			$('.define-wrapper').css('display', 'block');
		}
	});
}

function getSkipedCol()
{
	let skiped = [];

	skiped.push($('[name="entity_info_draft"]').val());
	skiped.push($('[name="entity_info_waiting_for"]').val());
	skiped.push($('[name="entity_info_approval_note"]').val());
	skiped.push($('[name="entity_info_approval_id"]').val());

	skiped.push($('[name="entity_info_admin_create"]').val());
	skiped.push($('[name="entity_info_admin_edit"]').val());
	skiped.push($('[name="entity_info_admin_ask_edit"]').val());

	skiped.push($('[name="entity_info_time_create"]').val());
	skiped.push($('[name="entity_info_time_edit"]').val())
	skiped.push($('[name="entity_info_time_ask_edit"]').val());

	skiped.push($('[name="entity_info_ip_create"]').val());
	skiped.push($('[name="entity_info_ip_edit"]').val());
	skiped.push($('[name="entity_info_ip_ask_edit"]').val());
	return skiped;
}

function generateSelectFilter(field, args)
{
	var virtualDOM;
	
	args = args || {};
	args.type = args.type || 'text';
	var dataType = args.type;
	var matchByType = {
		'FILTER_SANITIZE_NUMBER_INT':['bit', 'varbit', 'smallint', 'int', 'integer', 'bigint', 'smallserial', 'serial', 'bigserial', 'bool', 'boolean'],
		'FILTER_SANITIZE_NUMBER_FLOAT':['numeric', 'double', 'real', 'money'],
		'FILTER_SANITIZE_SPECIAL_CHARS':['char', 'character', 'varchar', 'character varying', 'text', 'date', 'timestamp', 'time']
	}
	
	virtualDOM = $(
	'<select class="form-control input-data-filter" name="filter_type_'+field+'" id="filter_type_'+field+'">\r\n'+
		'<option value="FILTER_SANITIZE_NUMBER_INT">NUMBER_INT</option>\r\n'+
		'<option value="FILTER_SANITIZE_NUMBER_UINT">NUMBER_UINT</option>\r\n'+
		'<option value="FILTER_SANITIZE_NUMBER_OCTAL">NUMBER_OCTAL</option>\r\n'+
		'<option value="FILTER_SANITIZE_NUMBER_HEXADECIMAL">NUMBER_HEXADECIMAL</option>\r\n'+
		'<option value="FILTER_SANITIZE_NUMBER_FLOAT">NUMBER_FLOAT</option>\r\n'+
		'<option value="FILTER_SANITIZE_STRING">STRING</option>\r\n'+
		'<option value="FILTER_SANITIZE_STRING_INLINE">STRING_INLINE</option>\r\n'+
		'<option value="FILTER_SANITIZE_NO_DOUBLE_SPACE">NO_DOUBLE_SPACE</option>\r\n'+
		'<option value="FILTER_SANITIZE_STRIPPED">STRIPPED</option>\r\n'+
		'<option value="FILTER_SANITIZE_SPECIAL_CHARS">SPECIAL_CHARS</option>\r\n'+
		'<option value="FILTER_SANITIZE_ALPHA">ALPHA</option>\r\n'+
		'<option value="FILTER_SANITIZE_ALPHANUMERIC">ALPHANUMERIC</option>\r\n'+
		'<option value="FILTER_SANITIZE_ALPHANUMERICPUNC">ALPHANUMERICPUNC</option>\r\n'+
		'<option value="FILTER_SANITIZE_STRING_BASE64">STRING_BASE64</option>\r\n'+
		'<option value="FILTER_SANITIZE_EMAIL">EMAIL</option>\r\n'+
		'<option value="FILTER_SANITIZE_URL">URL</option>\r\n'+
		'<option value="FILTER_SANITIZE_IP">IP</option>\r\n'+
		'<option value="FILTER_SANITIZE_ENCODED">ENCODED</option>\r\n'+
		'<option value="FILTER_SANITIZE_COLOR">COLOR</option>\r\n'+
		'<option value="FILTER_SANITIZE_MAGIC_QUOTES">MAGIC_QUOTES</option>\r\n'+
		'<option value="FILTER_SANITIZE_PASSWORD">PASSWORD</option>\r\n'+
	'</select>\r\n'
	);
	
	var i, j, k, l;
	var filterType = 'FILTER_SANITIZE_SPECIAL_CHARS';
	var found = false;
	for(i in matchByType)
	{
		j = matchByType[i];
		for(k in j)
		{
			if(dataType.indexOf(j[k]) != -1)
			{
				filterType = i;
				found = true;
				break;
			}
		}
		if(found)
		{
			break;
		}
	}
	virtualDOM.find('option').each(function(index, element) {
        $(this).removeAttr('selected');
    });
	virtualDOM.find('option[value="'+filterType+'"]').attr('selected', 'selected');
	return virtualDOM[0].outerHTML;
}


function generateSelectType(field, args)
{
	var virtualDOM;
	args = args || {};
	args.type = args.type || 'text';
	var dataType = args.type;
	var matchByType = {
		'int':['bit', 'varbit', 'smallint', 'int', 'integer', 'bigint', 'smallserial', 'serial', 'bigserial', 'bool', 'boolean'],
		'float':['numeric', 'double', 'real', 'money'],
		'text':['char', 'character', 'varchar', 'character varying', 'text'],
		'date':['date'],
		'datetime':['datetime', 'timestamp'],
		'time':['time']
	}

	virtualDOM = $(
	'<select class="form-control input-field-data-type" name="data_type_'+field+'" id="data_type_'+field+'">\r\n'+
		'<option value="text" title="&lt;input type=&quot;text&quot;&gt;">text</option>\r\n'+
		'<option value="email" title="&lt;input type=&quot;email&quot;&gt;">email</option>\r\n'+
		'<option value="url" title="&lt;input type=&quot;email&quot;&gt;">url</option>\r\n'+
		'<option value="tel" title="&lt;input type=&quot;tel&quot;&gt;">tel</option>\r\n'+
		'<option value="password" title="&lt;input type=&quot;password&quot;&gt;">password</option>\r\n'+
		'<option value="int" title="&lt;input type=&quot;number&quot;&gt;">int</option>\r\n'+
		'<option value="float" title="&lt;input type=&quot;number&quot; step=&quot;any&quot;&gt;">float</option>\r\n'+
		'<option value="date" title="&lt;input type=&quot;text&quot;&gt;">date</option>\r\n'+
		'<option value="time" title="&lt;input type=&quot;text&quot;&gt;">time</option>\r\n'+
		'<option value="datetime" title="&lt;input type=&quot;text&quot;&gt;">datetime</option>\r\n'+
		'<option value="color" title="&lt;input type=&quot;text&quot;&gt;">color</option>\r\n'+
	'</select>\r\n'
	);
	
	var i, j, k, l;
	var filterType = 'FILTER_SANITIZE_SPECIAL_CHARS';
	var found = false;
	for(i in matchByType)
	{
		j = matchByType[i];
		for(k in j)
		{
			if(dataType.indexOf(j[k]) != -1)
			{
				filterType = i;
				found = true;
				break;
			}
		}
		if(found)
		{
			break;
		}
	}
	virtualDOM.find('option').each(function(index, element) {
        $(this).removeAttr('selected');
    });
	virtualDOM.find('option[value="'+filterType+'"]').attr('selected', 'selected');
	return virtualDOM[0].outerHTML;
}

function arrayUnique(arr1)
{
	var i;
	var arr2 = [];
	for(i = 0; i < arr1.length; i++)
	{
		if(arr2.indexOf(arr1[i]) == -1)
		{
			arr2.push(arr1[i]);
		}
	}
	return arr2;
}

String.prototype.equalIgnoreCase = function(str)
{
	var str1 = this;
	if(str1.toLowerCase() == str.toLowerCase())
	return true;
	return false;
}
function isKeyWord(str)
{
	str = str.toString();
	var i, j;
	var kw = keyWords.split(",");
	for(i in kw)
	{
		if(str.equalIgnoreCase(kw[i]))
		{
			return true;
		}
	}
	return false;
}
var keyWords = "absolute,action,add,after,aggregate,alias,all,allocate,alter,analyse,analyze,and,any,are,array,as,asc,assertion,at,authorization,avg,before,begin,between,binary,bit,bit_length,blob,boolean,both,breadth,by,call,cascade,cascaded,case,cast,catalog,char,character,character_length,char_length,check,class,clob,close,coalesce,collate,collation,column,commit,completion,connect,connection,constraint,constraints,constructor,continue,convert,corresponding,count,create,cross,cube,current,current_date,current_path,current_role,current_time,current_timestamp,current_user,cursor,cycle,data,date,day,deallocate,dec,decimal,declare,default,deferrable,deferred,delete,depth,deref,desc,describe,descriptor,destroy,destructor,deterministic,diagnostics,dictionary,disconnect,distinct,do,domain,double,drop,dynamic,each,else,end,end-exec,equals,escape,every,except,exception,exec,execute,exists,external,extract,false,fetch,first,float,for,foreign,found,free,from,full,function,general,get,global,go,goto,grant,group,grouping,having,host,hour,identity,ignore,immediate,in,indicator,initialize,initially,inner,inout,input,insensitive,insert,int,integer,intersect,interval,into,is,isolation,iterate,join,key,language,large,last,lateral,leading,left,less,level,like,limit,local,localtime,localtimestamp,locator,lower,map,match,max,min,minute,modifies,modify,month,names,national,natural,nchar,nclob,new,next,no,none,not,null,nullif,numeric,object,octet_length,of,off,offset,old,on,only,open,operation,option,or,order,ordinality,out,outer,output,overlaps,pad,parameter,parameters,partial,path,placing,position,postfix,precision,prefix,preorder,prepare,preserve,primary,prior,privileges,procedure,public,read,reads,real,recursive,ref,references,referencing,relative,restrict,result,return,returns,revoke,right,role,rollback,rollup,routine,row,rows,savepoint,schema,scope,scroll,search,second,section,select,sequence,session,session_user,set,sets,size,smallint,some,space,specific,specifictype,sql,sqlcode,sqlerror,sqlexception,sqlstate,sqlwarning,start,state,statement,static,structure,substring,sum,system_user,table,temporary,terminate,than,then,time,timestamp,timezone_hour,timezone_minute,to,trailing,transaction,translate,translation,treat,trigger,trim,true,under,union,unique,unknown,unnest,update,upper,usage,user,using,value,values,varchar,variable,varying,view,when,whenever,where,with,without,work,write,year,zone";

String.prototype.replaceAll = function(str1, str2, ignore) 
{
    return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
};
String.prototype.capitalize = function()
{
    return this.replace(/\w\S*/g, function(txt){
		return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
}
String.prototype.prettify = function()
{
	var i, j, k;
	var str = this;
	var arr = str.split(" ");
	for(i in arr)
	{
		j = arr[i];
		switch(j)
		{
			case "Id": arr[i] = "";
			break;
			case "Ip": arr[i] = "IP";
			break;
		}
	}
	return arr.join(" ");
}

function generateRow(field, args, skipedOnInsertEdit)
{
	let isKW = isKeyWord(field);
	let classes = [];
	let cls = '';
	classes.push('row-column');
	if(isKW)
	{
		classes.push('reserved');
	}
	cls = ' class="'+classes.join(' ')+'"';
	let insertRow = '';
	let editRow = '';
	let listRow = '';
	if($.inArray(field, skipedOnInsertEdit) != -1)
	{
		insertRow = '  <td align="center"><input type="checkbox" class="include_insert" name="include_insert_'+field+'" value="0" disabled="disabled"></td>\r\n';
		editRow = '  <td align="center"><input type="checkbox" class="include_edit" name="include_edit_'+field+'" value="0" disabled="disabled"></td>\r\n';
		listRow = '  <td align="center"><input type="checkbox" class="include_list" name="include_list_'+field+'" value="1"></td>\r\n';
	}
	else
	{
		insertRow = '  <td align="center"><input type="checkbox" class="include_insert" name="include_insert_'+field+'" value="1" checked="checked"></td>\r\n';
		editRow = '  <td align="center"><input type="checkbox" class="include_edit" name="include_edit_'+field+'" value="1" checked="checked"></td>\r\n';
		listRow = '  <td align="center"><input type="checkbox" class="include_list" name="include_list_'+field+'" value="1" checked="checked"></td>\r\n';
	}

	var rowHTML =
	'<tr data-field-name="'+field+'" '+cls+'>\r\n'+
	'  <td class="field-name">'+field+'<input type="hidden" name="field" value="'+field+'"></td>\r\n'+
	'  <td><input class="form-control input-field-name" type="text" name="caption_'+field+'" value="'+field.replaceAll("_", " ").capitalize().prettify().trim()+'" autocomplete="off" spellcheck="false"></td>\r\n'+
	insertRow+
	editRow+
	'  <td align="center"><input type="checkbox" class="include_detail" name="include_detail_'+field+'" value="1" checked="checked"></td>\r\n'+
	listRow+
	'  <td align="center"><input type="checkbox" class="include_key" name="include_key_'+field+'" value="1"></td>\r\n'+
	'  <td align="center"><input type="checkbox" class="include_required" name="include_required_'+field+'" value="1"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="text" checked="checked"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="textarea"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="checkbox"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="select"></td>\r\n'+
	'  <td align="center"><input type="hidden" class="reference-data" name="reference_data_'+field+'" value="{}"><button type="button" class="btn btn-sm btn-primary reference-button reference-button-data">Source</button></td>\r\n'+
	'  <td align="center"><input type="checkbox" name="list_filter_'+field+'" value="text" class="input-field-filter"></td>\r\n'+
	'  <td align="center"><input type="checkbox" name="list_filter_'+field+'" value="select" class="input-field-filter"></td>\r\n'+
	'  <td align="center"><input type="hidden" class="reference-filter" name="reference_filter_'+field+'" value="{}"><button type="button" class="btn btn-sm btn-primary reference-button reference-button-filter">Source</button></td>\r\n'+
	'  <td>\r\n'+
	generateSelectType(field, args)+
	'  </td>\r\n'+
	'  <td>\r\n'+
	generateSelectFilter(field, args)+
	'  </td>\r\n'+
	'</tr>\r\n';
	return rowHTML;
}



function serializeForm()
{
  let type = null;
  $('.reference_type').each(function(e2){
	  if($(this)[0].checked)
	  {
		type = $(this).val();
	  }
  });
  let entity = getEntityData();
  let map = getMapData();
  let yesno = null;
  let truefalse = null;
  let onezero = null;
  let all = {
	type: type,
	entity: entity,
	map: map,
	yesno: yesno,
	truefalse: truefalse,
	onezero: onezero
  };

  return all

}

function deserializeForm(data)
{
	$('#modal-create-reference-data').find('.modal-body').empty();
	$('#modal-create-reference-data').find('.modal-body').append(getReferenceResource());
	selectReferenceType(data);
	setEntityData(data);
	setMapData(data)
}

function addRow(table)
{
  let nrow = table.find('tbody').find('tr').length;
  let lastRow = table.find('tbody').find('tr:last-child').prop('outerHTML');
  table.find('tbody').append(lastRow);
}

function addColumn(table)
{
  let ncol = table.find('thead').find('tr').find('td').length;
  let nrow = table.find('tbody').find('tr').length;
  let pos = ncol - parseInt(table.attr('data-offset')) - 1;
  let inputHeader = '<td><input class="form-control map-key" type="text" value="" placeholder="Additional attribute name"></td>';
  let inputBody = '<td><input class="form-control map-value" type="text" value="" placeholder="Additional attribute value"></td>';
  table.find('thead').find('tr').find('td:nth('+pos+')').after(inputHeader);
  table.find('tbody').find('tr').each(function(e3){
	$(this).find('td:nth('+pos+')').after(inputBody);
  });
  table.find('tfoot').find('tr').find('td').attr('colspan', table.find('thead').find('tr').find('td').length);
}

function removeLastColumn(table)
{
  let ncol = table.find('thead').find('tr').find('td').length;
  let nrow = table.find('tbody').find('tr').length;
  let offset = parseInt(table.attr('data-offset'));
  let pos = ncol - offset - 1;
  if(ncol > (offset + 2))
  {
	table.find('thead').find('tr').find('td:nth('+pos+')').remove();
	table.find('tbody').find('tr').each(function(e3){
	  $(this).find('td:nth('+pos+')').remove();
	});
	table.find('tfoot').find('tr').find('td').attr('colspan', table.find('thead').find('tr').find('td').length);
  }
}

function selectReferenceType(data)
{
	let referenceType = data.type ? data.type : 'entity';
	if($('.reference_type[value="'+referenceType+'"]').length > 0)
	{
		$('.reference_type[value="'+referenceType+'"]')[0].checked = true;
	}
	$('.reference-section').css({'display':'none'});
	if(referenceType == 'entity')
	{
		$('.entity-section').css({'display':'block'});
	}
	else if(referenceType == 'map')
	{
		$('.map-section').css({'display':'block'});
	}
}



function setEntityData(data)
{
	data.entity = (data && data.entity) ? data.entity : {}; 
	let entity = data.entity;
	entity.entityName = entity.entityName ? entity.entityName : '';
	entity.tableName = entity.tableName ? entity.tableName : '';
	entity.primaryKey = entity.primaryKey ? entity.primaryKey : '';
	entity.value = entity.value ? entity.value : '';
	entity.objectName = entity.objectName ? entity.objectName : '';
	entity.propertyName = entity.propertyName ? entity.propertyName : '';

	let selector = '[data-name="entity"]';
	$(selector).find('.rd-entity-name').val(entity.entityName);
	$(selector).find('.rd-table-name').val(entity.tableName);
	$(selector).find('.rd-primary-key').val(entity.primaryKey);
	$(selector).find('.rd-value-column').val(entity.value);
	$(selector).find('.rd-reference-object-name').val(entity.objectName);
	$(selector).find('.rd-reference-property-name').val(entity.propertyName);

	setSpecificationData(data);
	setSortableData(data);
	setAdditionalOutputData(data)
}

function getEntityData()
{
  let selector = '[data-name="entity"]';
  let entity = {
	entityName: $(selector).find('.rd-entity-name').val().trim(),
	tableName: $(selector).find('.rd-table-name').val().trim(),
	primaryKey: $(selector).find('.rd-primary-key').val().trim(),
	value: $(selector).find('.rd-value-column').val().trim(),
	objectName: $(selector).find('.rd-reference-object-name').val().trim(),
	propertyName: $(selector).find('.rd-reference-property-name').val().trim(),
	specification: getSpecificationData(),
	sortable: getSortableData(),
	additionalOutput: getAdditionalOutputData()
  };
  return entity;
}

function setSpecificationData(data)
{
  let result = [];
  let selector = '[data-name="specification"]';
  let table = $(selector);
  let specification = data.entity.specification;
  if(typeof specification != 'undefined' && specification != null && specification.length > 0)
  {
	for(let i in specification)
	{
	  if(i > 0)
	  {
		addRow(table);
	  }
	  let tr = table.find('tr:last-child');
	  let row = specification[i];
	  tr.find('.rd-column-name').val(row.column);
	  tr.find('.rd-value').val(row.value);
	}
  }
}

function getSpecificationData()
{
  let result = [];
  let selector = '[data-name="specification"]';
  $(selector).find('tbody').find('tr').each(function(e){
	let tr = $(this);
	let column = tr.find('.rd-column-name').val().trim();
	let value = tr.find('.rd-value').val().trim();
	if(column.length > 0)
	{
	  result.push({
		column: column,
		value: fixValue(value)
	});
	}
  });
  return result;
}

function setSortableData(data)
{
  let result = [];
  let selector = '[data-name="sortable"]';
  let table = $(selector);
  let sortable = data.entity.sortable;
  if(typeof sortable != 'undefined' && sortable != null && sortable.length > 0)
  {
	for(let i in sortable)
	{
	  if(i > 0)
	  {
		addRow(table);
	  }
	  let tr = table.find('tr:last-child');
	  let row = sortable[i];
	  tr.find('.rd-column-name').val(row.sortBy);
	  tr.find('.rd-order-type').val(row.sortType);
	}
  }
}

function getSortableData()
{
  let result = [];
  let selector = '[data-name="sortable"]';
  $(selector).find('tbody').find('tr').each(function(e){
	let tr = $(this);
	let sortBy = tr.find('.rd-column-name').val().trim();
	let sortType = tr.find('.rd-order-type').val().trim();
	if(sortBy.length > 0)
	{
	  result.push({
		sortBy: sortBy,
		sortType: sortType
	});
	}
  });
  return result;
}

function setAdditionalOutputData(data)
{
  let result = [];
  let selector = '[data-name="additional-output"]';
  let table = $(selector);
  let additional = data.entity.additionalOutput;
  if(typeof additional != 'undefined' && additional != null && additional.length > 0)
  {
	for(let i in additional)
	{
	  if(i > 0)
	  {
		addRow(table);
	  }
	  let tr = table.find('tr:last-child');
	  let row = additional[i];
	  tr.find('.rd-column-name').val(row.column);
	}
  }
}

function getAdditionalOutputData()
{
  let result = [];
  let selector = '[data-name="additional-output"]';
  $(selector).find('tbody').find('tr').each(function(e){
	let tr = $(this);
	let column = tr.find('.rd-column-name').val().trim();
	if(column.length > 0)
	{
	  result.push({
		column: column
	});
	}
  });
  return result;
}

function setMapData(data)
{
  let result = [];
  let selector = '[data-name="map"]';
  let table = $(selector);
  let keys = [];
  data.map = data.map ? data.map : [];
  let map = data.map;
  if(map.length > 0)
  {
	let map0 = map[0];
	let objLength = 0;
	for(let i in map0)
	{
	  if(map0.hasOwnProperty(i))
	  {
		objLength++;
		if(objLength > 4)
		{
		  addColumn(table);
		}
		if(i != 'value' && i != 'label' && i != 'default')
		{
		  keys.push(i)
		}
	  }
	}
	for(let i in keys)
	{

	}
	for(let i in keys)
	{
	  let j = parseInt(i) + 1;
	  table.find('thead').find('tr').find('.map-key:nth-child('+j+')').val(keys[i]);
	}
	if($(selector).find('thead').find('tr').find('.map-key').length > 0)
	{
	  $(selector).find('thead').find('tr').find('.map-key').each(function(e){
		keys.push($(this).val().trim());
	  });
	}

	for(let i in map)
	{
	  if(i > 0)
	  {
		addRow(table);
	  }
	  let tr = table.find('tr:last-child');
	  let row = map[i];
	  tr.find('.rd-value').val(row.value);
	  tr.find('.rd-label').val(row.label);
	  if(map[i]['default'])
	  {
		tr.find('.rd-selected')[0].checked = true;
	  }

	  for(let k in keys)
	  {
		let j = parseInt(k) + 1;
		tr.find('.map-value:nth-child('+j+')').val(map[i][keys[k]]);
	  }
	}
  }
}

function getMapData()
{
  let result = [];
  let selector = '[data-name="map"]';
  let keys = [];
  if($(selector).find('thead').find('tr').find('.map-key').length > 0)
  {
	$(selector).find('thead').find('tr').find('.map-key').each(function(e){
	  keys.push($(this).val().trim());
	});
  }
  $(selector).find('tbody').find('tr').each(function(e){
	let tr = $(this);
	let value = tr.find('.rd-value').val().trim();
	let label = tr.find('.rd-label').val().trim();
	let selected = tr.find('.rd-selected')[0].checked;
	let opt = {
	  value: value,
	  label: label,
	  default: selected
	};
	if(keys.length > 0)
	{
	  let idx = 0;
	  tr.find('.map-value').each(function(e){
		let attrVal = $(this).val();
		if(keys[idx].length > 0)
		{
		  opt[keys[idx]] = attrVal;
		}
		idx++;
	  });
	}
	result.push(opt);
  });
  return result;
}

function fixValue(value)
{
  if(value == 'true')
  {
	return true;
  }
  else if(value == 'false')
  {
	return false;
  }
  else if(value == 'null')
  {
	return null;
  }
  else if(isNumeric(value))
  {
	return parseNumber(value);
  }
  else
  {
	return value;
  }
}

function parseNumber(str)
{
  if(str.indexOf('.') !== -1)
  {
	return parseFloat(str);
  }
  else
  {
	return parseInt(str);
  }
}

function isNumeric(str) {
  if (typeof str != "string") return false 
  return !isNaN(str) &&
		!isNaN(parseFloat(str))
}