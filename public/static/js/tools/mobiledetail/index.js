$(document).ready(function() {
    var mobiledetailViewModel = function() {
        var self = this;
        self.params = {}
        
        self.items = ko.observableArray();
        self.totalRecords = 0;
        self.loadItems = function(params){
            var ret = [];
            var pages = 0;//总页数
            $.ajax({
                url: baseUrl+'/tools/mobiledetail/getTbItems',
                data: JSON.stringify(params),
                type: 'post',
                contentType:'application/json; charset=utf-8',
                async: false,
                dataType: 'json',
                success: function (res) {
                	console.log(res);
                    if (res.success == true) {
                        ret = res.result;
                        if (ret == '') {
                            ret = {
                                'total_records':0,
                                'data':[]
                            }
                        }
                        if (ret.data) {
                        	
                        }
                        self.items(ret.data);
                        self.totalRecords = ret.total_records;
                        console.log(self.params.page_size);
                        pages = Math.ceil(self.totalRecords/self.params.page_size);
                        pageModel.show(self.params.page_num,pages);
                    } else {
                        $.alert({
                            title: '什么鬼，系统出错了',
                            body: res.error_msg,
                        });
                    }
                },
                error:function (res) {
                    $.alert({
                        title: '哎哟妈呀，系统出错了',
                        body: res.error_msg,
                    });
                }
            });
        }
        
        self.search = function(approveStatus){
        	self.params.q = $("#q").val();
        	if (approveStatus != '' && approveStatus != undefined) {
        		self.params.approve_status = approveStatus; 
        	}
        	self.loadItems(self.params);
        }
        
    };
    
    var mobiledetailM = new mobiledetailViewModel();
    ko.applyBindings(mobiledetailM);
    
    //参数初始化
    mobiledetailM.params = {
    	'page_size' : 10,
    	'page_num' : 1,
    };
//  mobiledetailM.params = {
//  'page_size' : pageSize,
//  'page_num' : pageNum,//页码
//  'q' : q,//搜索宝贝
//  'approve_status' : aproveStatus, //商品上传后的状态,出售中`ONSALE`,仓库中`INSTOCK`
//  'cid' : cid //店铺分类
//};
    
    var pageModel = {
    	show : function(pageNum,pages) {
    		$('.my-page').pagination({
    	        styleClass: ['pagination-xlarge'],
    	        showCtrl: true,
    	        displayPage: 6,
    	        onSelect: function (num) {
    	        	mobiledetailM.params.page_num = num;
    	            mobiledetailM.loadItems(mobiledetailM.params);
    	        }        
    	    });
    		$('.my-page').pagination('updatePages', pages, pageNum);
    	}
    	
    }
    
    mobiledetailM.loadItems(mobiledetailM.params);

    
    
    
    
})