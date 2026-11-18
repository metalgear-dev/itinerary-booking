function createElementFromHTML(htmlString) {
  var div = document.createElement('div');
  div.innerHTML = htmlString.trim();

  // Change this to div.childNodes to support multiple top-level nodes
  return div.firstChild;
}

document.insertAfter = function(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
};

let WidgetController = function(widget_id) {
  this._state = {
    widget_id: widget_id,
    inquiry_id: null,
    reservation_passed: false
  };
  this.widget_id = widget_id;
  this._option = {
    meta: [],
    widgets: [],
    services: []
  };
  this._listeners = {};
  window.itnWidget = this;
};

WidgetController.prototype.getOption = function() {
  return this._option;
};
WidgetController.prototype.setOption = function(o) {
  this._option = o;
};
WidgetController.prototype.getState = function() {
  return this._state;
};

WidgetController.prototype.setState = function(state) {
  Object.assign(this._state, state);
};
WidgetController.prototype.getEventListeners = function() {
  return this._listeners;
};
WidgetController.prototype.readCookie = function(name) {
  var nameEQ = getCookieName(name) + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1,c.length);
    }
    if (c.indexOf(nameEQ) === 0) {
      return c.substring(nameEQ.length,c.length);
    }
  }
  return null;
};
WidgetController.prototype.setCookie = function(name, value, days = 0 ) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = getCookieName(name) + "=" + (value || "")  + expires + "; path=/";
};
WidgetController.prototype.addEventListener = function(e, callable) {
  let type = undefined;
  if (typeof e == 'string') {
    type = e;
  } else if (e instanceof Event) {
    type = e.type;
  }
  if (type !== undefined) {
    if (this._listeners[type]) {
    } else {
      this._listeners[type] = [];
    }
    this._listeners[type].push({
      event: e, callable
    });
  }
  return this;
};
WidgetController.prototype.removeEventListener = function(e) {
  let type = undefined;
  if (typeof e == 'string') {
    type  = e;
  } else if (e instanceof Event) {
    type = e.type;
  }
  if (type !== undefined) {
    this._listeners[e] = undefined;
  }
  return this;
};
WidgetController.prototype.dispatchEvent = function(e) {
  let type = undefined;
  if (typeof e == 'string') {
    type = e;
  } else if (e instanceof Event) {
    type = e.type;
  }
  if (this._listeners[type]) {
    this._listeners[type].forEach(function(bind) {
      bind.callable(e);
    });
  }
  return this;
};
WidgetController.prototype.findMyTag = function() {
  let scripts = document.getElementsByTagName("script");
  let myTag = false;
  console.log(scripts);
  for (let scriptTag of scripts) {
    if (scriptTag.src && scriptTag.src.endsWith("widget-controller.js")) {
      myTag = scriptTag;
    }
  };
  return myTag;
};
WidgetController.prototype.init = function() {
  let self = this;
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      let data = JSON.parse(this.responseText);
      if (!data.success) {
        throw new Error(data.message);
      }
      self._option = data;
      self.run();
    }
  };
    xhr.open("GET", `https://tk2-237-28163.vs.sakura.ne.jp/api/getWidgetInfo/${self.widget_id}`);
  xhr.send();
  let cssTag = document.createElement("link");
  cssTag.setAttribute("rel", "stylesheet");
  cssTag.setAttribute("type", "text/css");
  cssTag.setAttribute("href", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css");
  cssTag.setAttribute("media", "all");
  document.head.appendChild(cssTag);
  cssTag = document.createElement("link");
  cssTag.setAttribute("rel", "stylesheet");
  cssTag.setAttribute("type", "text/css");
  cssTag.setAttribute("href", "https://redux-form.com/6.6.2/bundle.css");
  cssTag.setAttribute("media", "all");
  document.head.appendChild(cssTag);
};

WidgetController.prototype.run = function() {
  console.log("Widget controller is running.");
  console.log("state is as follows:");
  console.log(this.getState());
  console.log("option is as follows:");
  console.log(this.getOption());
  let self = this;

  // if state has not inquiry id but cookie has itn_inquiry_id
  if (!(this.getState().inquiry_id) && this.readCookie('itn_inquiry_id')) {
    console.log('receiving inquiry id from cookie...');
    const inquiry_id = this.readCookie('itn_inquiry_id');

    // if inquiry reservation date is passed or not
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (this.readyState === 4 && this.status === 200) {
        let data = JSON.parse(this.responseText);
        console.log("RESERVATION DATE RESULT");
        console.log(data.reservation_date);

        // if inquiry data exists
        if (data.reservation_date !== "none") {
          self.setState({
            inquiry_id: inquiry_id
          });
          // if reservation date passed
          if(data.reservation_date === "true"){
            self.setState({
              reservation_passed: true
            })
          }
        }

        let option = self.getOption();
        option.meta.forEach(function(data) {
          if (data.name === 'itinerary-tour') {
            tourMetaExists = true;
            data.content = self.readCookie('itn_tour_id');
          }
          if (self.getState().reservation_passed && data.name === 'widget-service') {
            const jsonConfig = JSON.parse(data.content);
            let services = [];
            for (let key in jsonConfig) {
              if (jsonConfig[key]) {
                services.push(key);
              }
            }
            self._option.services = services;
          }
          if (!self.getState().reservation_passed && data.name === 'chat-service') {
            const jsonConfig = JSON.parse(data.content);
            let services = [];
            for (let key in jsonConfig) {
              if (jsonConfig[key]) {
                services.push(key);
              }
            }
            self._option.services = services;
          }
        });
        if (!tourMetaExists) {
          option.meta.push({
            name: 'itinerary-tour',
            content: self.readCookie('itn_tour_id')
          });
        }
        console.log("widget controller state is as follows:");
        console.log(self.getState());
        self.setOption(option);
        console.log("widget controller option is as follows:");
        console.log(self.getOption());

        self.postProcessor();
      }

    };
    xhr.open("POST", `https://tk2-237-28163.vs.sakura.ne.jp/api/getReservationDate`);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({inquiry_id : inquiry_id}));


  }else{
    self.postProcessor();
  }


};

WidgetController.prototype.postProcessor = function(){
  let self = this;
  let myTag = self.findMyTag();
  let domHead = document.head;

  // if state has inquiry id
  if (this.getState().inquiry_id) {
    console.log('inquiry id found: ' + this.getState().inquiry_id);
    if (inquiryForm = document.getElementById("widgetInquiryForm")) {
      inquiryForm.remove();
    }
    domHead.append(createElementFromHTML(`<meta name="itinerary-inquiry" content="${self.getState().inquiry_id}" />`));
    let metas = this.getOption().meta;
    metas.forEach(function(meta) {
      let metaTag = createElementFromHTML(`<meta name="${meta.name}" content="${meta.content}"/>`);
      domHead.append(metaTag);
    });
    let widgets = this.getOption().widgets;

    if(this.getState().reservation_passed){
      if (!(document.querySelector("#widgetTourController"))) {
        let divTag = createElementFromHTML("<div id=\"widgetTourController\"></div>");
        document.insertAfter(divTag, myTag);
      }
    }
    if (!(document.querySelector("#widgetVoxeet"))) {
      divTag = createElementFromHTML("<div id=\"widgetVoxeet\"></div>");
      document.insertAfter(divTag, myTag);
    }
    widgets.forEach(function(widget) {
      let scriptTag = document.createElement("script");
      scriptTag.src = widget;
      document.head.appendChild(scriptTag);
    });

    // has not inquiry id and not in cookie either
  } else {

    // set inquiry form
    let divTag = createElementFromHTML("<div id=\"widgetInquiryForm\"></div>");
    document.insertAfter(divTag, myTag);
    let scriptTag = document.createElement("script");
    scriptTag.src = "https://tk2-237-28163.vs.sakura.ne.jp/module/itinerary/widgets/inquiry-form.js";
    document.head.appendChild(scriptTag);

    // set event handler when inquiry admitted
    self.removeEventListener('inquiry.submit').addEventListener('inquiry.submit', function (e) {
      if (e.detail.inquiry_id) {
        console.log("inquiry submitted data is:");
        console.log(e.detail);

        let url_path = window.location.pathname;
        let after_path = url_path.slice(1, url_path.length);

        self.setCookie('itn_user_name', e.detail.user_name);
        self.setCookie('itn_user_avatar', e.detail.user_avatar);
        self.setCookie('itn_inquiry_id', e.detail.inquiry_id);
        self.setCookie('itn_user_id', e.detail.user_id);
        self.setCookie('itn_tour_id', e.detail.tour_id);
        self.run();
      }
    });

  }

  if(self.getState().inquiry_id && !self.getState().reservation_passed){
    checkReservationInterval = setInterval(checkReservationPassed, 10000);
  }

};

// global interval function
let checkReservationInterval;
function checkReservationPassed(){
  let xhr = new XMLHttpRequest();
  let inquiry_id = readCookie('itn_inquiry_id');
  xhr.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      let data = JSON.parse(this.responseText);
      if (data.reservation_date === "true") {
        // self.setState({
        //   reservation_passed: true
        // });
        alert("予約時間を過ぎたため、診察室が開きました。");
        clearInterval(checkReservationInterval);
        window.location.reload();
      }
    }
  };
  xhr.open("POST", `https://tk2-237-28163.vs.sakura.ne.jp/api/getReservationDate`);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(JSON.stringify({inquiry_id : inquiry_id}));
}

readCookie = function(name){
  var nameEQ = getCookieName(name) + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1,c.length);
    }
    if (c.indexOf(nameEQ) === 0) {
      return c.substring(nameEQ.length,c.length);
    }
  }
  return null;
};

getCookieName = function(name){
  let after_path = window.location.pathname;
  let suffix_string = after_path.slice(1, after_path.length).replace(/\/$/, "").split("/").join("_");
  if(suffix_string !== ""){
    return name + "_" + suffix_string;
  }else{
    return name;
  }
};

