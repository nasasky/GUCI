const groupData = require('../../untils/groupData.js')
const Util = require('../../untils/util.js')

Page({
    data: {
        groupData: groupData
    },
    // 定义转发
    onShareAppMessage: Util.shareConfig,
    onLoad() {
        wx.setNavigationBarTitle({
            title: '梦境类别'
        })
    }
})
