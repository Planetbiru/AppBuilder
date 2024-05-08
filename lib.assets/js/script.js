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

		let masterPrimaryKeyName = $(this).find('option:selected').attr('data-primary-key') || '';
		
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
		let parentTd = $(this).closest('td'); 
		let parentTr = $(this).closest('tr'); 
		let fieldName = parentTr.attr('data-field-name');
		loadReference(fieldName);
		$('#modal-create-reference-data').modal('show');
	})

	loadTable();
});

function loadReference(fieldName)
{
	$.ajax({
		type:'GET',
		url:'lib.ajax/reference.php',
		data:{fieldName:fieldName},
		dataType:'json',
		success: function(data){
			console.log(data)
		}
	})
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

		let field = {
			fieldName:fieldName,
			fieldLabel:fieldLabel,
			includeInsert:includeInsert,
			includeEdit:includeEdit,
			includeDetail:includeDetail,
			includeList:includeList,
			isKey:isKey,
			isInputRequired:isInputRequired,
			elementType:elementType,
			filterElementType:filterElementType,
			dataType:dataType,
			inputFilter:inputFilter			
		};
		fields.push(field);
	});

	let requireApproval = $('#with_approval')[0].checked && true;
	let withTrash = $('#with_trash')[0].checked && true;
	let manualSortOrder = $('#manualsortorder')[0].checked && true;
	let actiavteDeactivate = $('#actiavte_deactivate')[0].checked && true;
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
		actiavteDeactivate: actiavteDeactivate,
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

function generateAllCode(dataToPost)
{
	$.ajax({
		type:'post', 
		url: 'lib.ajax/script-generator.php',
		dataType:'json',
		data:dataToPost,
		success: function(data)
		{
			
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
	if($.inArray(field, skipedOnInsertEdit) != -1)
	{
		insertRow = '  <td align="center"><input type="checkbox" class="include_insert" name="include_insert_'+field+'" value="0" disabled="disabled"></td>\r\n';
		editRow = '  <td align="center"><input type="checkbox" class="include_edit" name="include_edit_'+field+'" value="0" disabled="disabled"></td>\r\n';
	}
	else
	{
		insertRow = '  <td align="center"><input type="checkbox" class="include_insert" name="include_insert_'+field+'" value="1" checked="checked"></td>\r\n';
		editRow = '  <td align="center"><input type="checkbox" class="include_edit" name="include_edit_'+field+'" value="1" checked="checked"></td>\r\n';
	}

	var rowHTML =
	'<tr data-field-name="'+field+'" '+cls+'>\r\n'+
	'  <td class="field-name">'+field+'<input type="hidden" name="field" value="'+field+'"></td>\r\n'+
	'  <td><input class="form-control input-field-name" type="text" name="caption_'+field+'" value="'+field.replaceAll("_", " ").capitalize().prettify().trim()+'" autocomplete="off" spellcheck="false"></td>\r\n'+
	insertRow+
	editRow+
	'  <td align="center"><input type="checkbox" class="include_detail" name="include_detail_'+field+'" value="1" checked="checked"></td>\r\n'+
	'  <td align="center"><input type="checkbox" class="include_list" name="include_list_'+field+'" value="1" checked="checked"></td>\r\n'+
	'  <td align="center"><input type="checkbox" class="include_key" name="include_key_'+field+'" value="1"></td>\r\n'+
	'  <td align="center"><input type="checkbox" class="include_required" name="include_required_'+field+'" value="1"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="text" checked="checked"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="textarea"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="checkbox"></td>\r\n'+
	'  <td align="center"><input type="radio" class="input-element-type" name="element_type_'+field+'" value="select"></td>\r\n'+
	'  <td align="center"><button type="button" class="btn btn-sm btn-primary reference-button reference-button-data">Source</button></td>\r\n'+
	'  <td align="center"><input type="checkbox" name="list_filter_'+field+'" value="text" class="input-field-filter"></td>\r\n'+
	'  <td align="center"><input type="checkbox" name="list_filter_'+field+'" value="select" class="input-field-filter"></td>\r\n'+
	'  <td align="center"><button type="button" class="btn btn-sm btn-primary reference-button reference-button-filter">Source</button></td>\r\n'+
	'  <td>\r\n'+
	generateSelectType(field, args)+
	'  </td>\r\n'+
	'  <td>\r\n'+
	generateSelectFilter(field, args)+
	'  </td>\r\n'+
	'</tr>\r\n';
	return rowHTML;
}
