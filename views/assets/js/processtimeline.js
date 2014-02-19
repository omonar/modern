/* begin third party code */
// Andy E
// http://stackoverflow.com/users/94197/andy-e
// http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
Date.createFromMysql = function(mysql_string) {
  if(typeof mysql_string === 'string') {
    var t = mysql_string.split(/[- :]/);
    //when t[3], t[4] and t[5] are missing they defaults to zero
    return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
  }
  return null;
}
/* end third party code */

self.myframes = [];
self.timelinedata = new Array();

function processTimelineData() {
  for(camera in self.cameras) {
    self.myframes[camera] = new Array();
  };

  self.activity.forEach(function(v, i) {
    self.timelinedata.push({start: Date.createFromMysql(self.activity[i].StartTime), end: Date.createFromMysql(self.activity[i].EndTime), content: self.activity[i].Id, className: "monitor"+self.activity[i].MonitorId});

    if(typeof(self.myframes[Number(v.MonitorId)-1][Number(v.Id)]) === "undefined") {
      self.myframes[Number(v.MonitorId)-1][Number(v.Id)] = new Array();
    }
    i++;
  });
  postMessage("timelinedata###" + JSON.stringify(self.timelinedata));
  //postMessage("myframes###" + JSON.stringify(self.myframes));
  postMessage("success");
  return;
}

self.addEventListener('message', function(e) {
  if(e.data.indexOf("###") != -1) {
    var x = e.data.split("###");
    switch(x[0]) {
      case "cameras":
        self.cameras = JSON.parse(x[1]);
        break;
      case "activity":
        self.activity = JSON.parse(x[1]);
        break;
    }
  }
  else {
    processTimelineData();
  }
}, false);