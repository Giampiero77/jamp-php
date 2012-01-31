/**
* Class PAGINATION
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2011
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsPagination()
{
}

clsPagination.prototype = 
{
	page : function(dsObjName, page) 
	{
		var dsObj = $(dsObjName);
		dsObj.DSpre = dsObj.DSpos;
		dsObj.DSpos = 1;
		dsObj.DSstart = page;
		AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObj.id + "&start=" + page);
	},

	refreshObj : function(dsObjName)
	{
		var obj = $(dsObjName);
		obj.innerHTML = '';
		var dsObj = $(obj.p.dsObj);
		var res = dsObj.DSresult;
		if (res.length > 0)
		{
			 var block_page = 5; 
			 var limit = (dsObj.DSlimit == 0) ? 1 : dsObj.DSlimit;
			 var tot_pages = Math.ceil(dsObj.DSrow / limit);
			 var numpage = (dsObj.DSstart == 0) ? 1 : dsObj.DSstart;
			 var start_page = (Math.floor((numpage - 1) / block_page) * block_page) + 1;
			 var end_page = (start_page + block_page > tot_pages) ? (tot_pages + 1) : (start_page + block_page);
			 if (numpage > block_page) 
			 {
				  var prev = document.createElement('button');
				  prev.className = 'prevpostslink';
				  prev.innerHTML = '«';
				  prev.onclick = function() {PAGINATION.page(obj.p.dsObj, start_page - 1);}
				  obj.appendChild(prev);
			 }
			 for (var i = start_page; i < end_page; i++)
			 {
				  if (i == numpage) 
				  {
						var page = document.createElement('span');
						page.className = 'current';
						page.innerHTML = i;
						obj.appendChild(page);
				  }
				  else if (tot_pages > i)
				  {
						var page = document.createElement('button');
						page.className = 'page';
						page.onclick = function() {PAGINATION.page(obj.p.dsObj, this.innerHTML);}
						page.innerHTML = i;
						obj.appendChild(page);
				  }
			 }
			 if (tot_pages > numpage && tot_pages > end_page) 
			 {
				  var span = document.createElement('span');
				  span.innerHTML = ' ... ';
				  obj.appendChild(span);
			 }
			 if (tot_pages > numpage) 
			 {
				  var last = document.createElement('button');
				  last.className = 'page';
				  last.innerHTML = tot_pages;
				  last.onclick = function() {PAGINATION.page(obj.p.dsObj, tot_pages);}
				  obj.appendChild(last);
			 }
			 if (tot_pages > end_page) 
			 {
				  var next = document.createElement('button');
				  next.className = 'nextpostslink';
				  next.innerHTML = '»';
				  next.onclick = function() {PAGINATION.page(obj.p.dsObj, end_page);}
				  obj.appendChild(next);
			 }
		}
	}
}

var PAGINATION = new clsPagination();