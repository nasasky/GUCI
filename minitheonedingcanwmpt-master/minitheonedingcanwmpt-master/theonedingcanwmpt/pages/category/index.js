// import ApiList from  '../../config/api';
// import request from '../../utils/request.js';

Page({
    data: {
        // types: null,
        typeTree: {},       // 数据缓存
          currType: 0 ,
            // 当前类型
     "types": [
            {
                "name":"热销产品",
               "typeId":"0",
            },   
            {
          
                "name":"人气产品",
               "typeId":"1",
            },
            {
                "name":"店家推荐",
               "typeId":"2",
            }, 
        ],
     "typeTree": [
         
            {
               'pic':"../../images/im.jpg",
                "shopAddr":"飞马牌服饰",
                "name":"PUMA Kids",
               "typeId":"1",
            },   
         
            {
               'pic':"../../images/im.jpg",
                "shopAddr":"飞马牌服饰",
                "name":"PUMA Kids",
               "typeId":"1",
            },   
                     
            {
               'pic':"../../images/im.jpg",
                "shopAddr":"飞马牌服饰",
                "name":"PUMA Kids",
               "typeId":"1",
            },   
        ],


    },
        
    onLoad (){
        var me = this;
        request({
            url: ApiList.goodsType,
            success: function (res) {
                me.setData({
                    types: res.data.data
                });
            }
        });
  this.getTypeTree(this.data.currType);
    },
 tapType(e){
   const currType = e.currentTarget.dataset.typeId;
        this.setData({
            currType: currType
        });
        this.getTypeTree(currType);
    },
    // 加载品牌、二级类目数据
    getTypeTree (currType) {
        const me = this, _data = me.data;
        if(!_data.typeTree[currType]){
            request({
                url: ApiList.goodsTypeTree,
                data: {typeId: +currType},
                success: function (res) {
                    _data.typeTree[currType] = res.data.data;
                    me.setData({
                        typeTree: _data.typeTree
                    });
                }
            });
        }
    }
})