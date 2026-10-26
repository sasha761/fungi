
// let modulesLoaded = false;

// export function setModulesLoaded(value) {
//   modulesLoaded = value;
// }

// export function isModulesLoaded() {
//   return modulesLoaded;
// }


const modulesLoader = (() => {
  let modulesLoaded = false;

  return {
    setModulesLoaded(value) {
      modulesLoaded = value;
    },
    isModulesLoaded() {
      return modulesLoaded;
    }
  };
})();

export default modulesLoader;
