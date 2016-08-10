/**
 * Created by baocaizai on 16/7/12.
 */
$(document).ready(function () {


    function search(start, end, route) {
        transit.search(start, end);
    }


    $("#button_bj").click(function () {
        var p1 = $("#suggestId").val();
        var p2 = $("#suggestId2").val();
        search(p1, p2, null);
        $.post("?m=home&c=index&a=bj",
            {
                lat: p1,
                lng: p2
            },
            function (data, status) {

            });
    });


    function G(id) {
        return document.getElementById(id);
    }

    var map = new BMap.Map("l-map");


    function addMarker(point, label) {
        var marker = new BMap.Marker(point);
        map.addOverlay(marker);
    }

    var geoc = new BMap.Geocoder();
    var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
        {
            "input": "suggestId"
            , "location": map
        });
    var ac2 = new BMap.Autocomplete(    //建立一个自动完成的对象
        {
            "input": "suggestId2"
            , "location": map
        });


    ac.addEventListener("onhighlight", function (e) {  //鼠标放在下拉列表上的事件
        var str = "";
        var _value = e.fromitem.value;
        var value = "";
        if (e.fromitem.index > -1) {
            value = _value.province + _value.city + _value.district + _value.street + _value.business;
        }
        str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.province + _value.city + _value.district + _value.street + _value.business;
        }
        str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    });

    ac2.addEventListener("onhighlight", function (e) {  //鼠标放在下拉列表上的事件
        var str = "";
        var _value = e.fromitem.value;
        var value = "";
        if (e.fromitem.index > -1) {
            value = _value.province + _value.city + _value.district + _value.street + _value.business;
        }
        str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.province + _value.city + _value.district + _value.street + _value.business;
        }
        str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    });

    var myValue;
    ac.addEventListener("onconfirm", function (e) {    //鼠标点击下拉列表后的事件
        var _value = e.item.value;
        myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
        G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;

        setPlace();
    });

    ac2.addEventListener("onconfirm", function (e) {    //鼠标点击下拉列表后的事件
        var _value = e.item.value;
        myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
        G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;

        setPlace();
    });


    var searchComplete = function (results) {
        if (transit.getStatus() != BMAP_STATUS_SUCCESS) {
            return;
        }
        var plan = results.getPlan(0);
        var dis = (plan.getDistance(false) / 1000).toFixed(0);

        if (dis <= 9) {
            $('#weui_dialog_msg').html(plan.getDistance(true) + '用【Uber】最划算');
            $('#weui_dialog').css('display', 'block');
        } else if (dis > 9 && dis <= 16) {
            $('#weui_dialog_msg').html(plan.getDistance(true) + '用【滴滴出行】最划算');
            $('#weui_dialog').css('display', 'block');
        } else if (dis >= 16) {
            $('#weui_dialog_msg').html(plan.getDistance(true) + '用【易道用车】最划算');
            $('#weui_dialog').css('display', 'block');
        }

    }

    var transit = new BMap.DrivingRoute(map, {
        renderOptions: {map: map},
        onSearchComplete: searchComplete

    });


    function setPlace() {
        map.clearOverlays();
        function myFun() {
            var pp = local.getResults().getPoi(0).point;
            map.centerAndZoom(pp, 18);
            var mk = new BMap.Marker(pp);
            mk.enableDragging();
            map.addOverlay(mk);    //添加标注

        }

        var local = new BMap.LocalSearch(map, { //智能搜索
            onSearchComplete: myFun
        });
        local.search(myValue);
    }


    var geolocation = new BMap.Geolocation();
    geolocation.getCurrentPosition(function (r) {
        if (this.getStatus() == BMAP_STATUS_SUCCESS) {
            var mk = new BMap.Marker(r.point);
            mk.enableDragging();
            map.addOverlay(mk);

            geoc.getLocation(r.point, function (rs) {
                var addComp = rs.addressComponents;
            });


            var current_point = new BMap.Point(r.point.lng, r.point.lat)
            $('#start_point').valueOf(current_point);

            map.centerAndZoom(current_point, 17);
            map.enableScrollWheelZoom(true);
            var myGeo = new BMap.Geocoder();
            myGeo.getLocation(current_point, function (result) {
                if (result) {
                    $('#suggestId').val(result.address);
                }
            });

        }
        else {
        }
    }, {enableHighAccuracy: false})


});
