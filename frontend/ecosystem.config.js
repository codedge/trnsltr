module.exports = {
  "apps" : {
    "name"        : "TRNSLTR",
    "script"      : "./server.js",
    "watch"       : true,
    "instances"  : 1,
    "exec_mode"  : "cluster",
    "env" : {
      "PORT": 8880
    },
    "env_production" : {
      "NODE_ENV": "production"
    },
  }
};
