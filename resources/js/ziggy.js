const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"admin.enter":{"uri":"adminenter","methods":["GET","HEAD"]},"admin.login":{"uri":"adminenter","methods":["POST"]},"admin.dashboard":{"uri":"adminenter\/dashboard","methods":["GET","HEAD"]},"admin.logout":{"uri":"adminenter\/logout","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
