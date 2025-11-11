import { renderToString } from '@vue/server-renderer';
import { mergeProps, useSSRContext, ref, onMounted, onUnmounted, computed, createVNode, resolveDynamicComponent, createSSRApp } from 'vue';
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent, ssrRenderClass, ssrRenderList, ssrRenderVNode } from 'vue/server-renderer';

const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};

const _sfc_main$5 = {
  __name: 'TestButton',
  __ssrInlineRender: true,
  props: {
  label: {
    type: String,
    default: 'Click me',
  },
},
  emits: ['click'],
  setup(__props, { emit: __emit }) {

return (_ctx, _push, _parent, _attrs) => {
  _push(`<button${
    ssrRenderAttrs(mergeProps({
      class: "button",
      type: "button"
    }, _attrs))
  } data-v-e24fc609>${
    ssrInterpolate(__props.label)
  }</button>`);
}
}

};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext()
  ;(ssrContext.modules || (ssrContext.modules = new Set())).add("components/TestButton.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : undefined
};
const TestButton = /*#__PURE__*/_export_sfc(_sfc_main$5, [['__scopeId',"data-v-e24fc609"]]);

const _sfc_main$4 = {
  __name: 'TestComponent',
  __ssrInlineRender: true,
  setup(__props) {

const count = ref(0);

function increment(event) {
  console.info('[TestComponent] increment handler fired', {
    current: count.value,
    next: count.value + 1,
    eventType: event?.type ?? null,
    eventDetail: event?.detail ?? null,
    timeStamp: event?.timeStamp ?? null,
    isTrusted: event?.isTrusted ?? null,
  });
  count.value += 1;
}

let captureListener = null;

onMounted(() => {
  console.info('[TestComponent] mounted', {
    componentId: Math.random().toString(36).slice(2, 8),
    initial: count.value,
  });
  captureListener = (evt) => {
    if (!evt.isTrusted) {
      console.info('[TestComponent] observed non-trusted click on window', {
        type: evt.type,
        detail: evt.detail,
        timeStamp: evt.timeStamp,
      });
    }
  };
  window.addEventListener('click', captureListener, { capture: true });
});

onUnmounted(() => {
  if (captureListener) {
    window.removeEventListener('click', captureListener, { capture: true });
    captureListener = null;
  }
});

return (_ctx, _push, _parent, _attrs) => {
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "test" }, _attrs))} data-v-204205aa><p data-v-204205aa>This is a simple test component. Click the button to increment the counter.</p>`);
  _push(ssrRenderComponent(TestButton, {
    label: `Clicked ${count.value} times`,
    onClick: increment
  }, null, _parent));
  _push(`</section>`);
}
}

};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext()
  ;(ssrContext.modules || (ssrContext.modules = new Set())).add("components/TestComponent.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : undefined
};
const TestComponent = /*#__PURE__*/_export_sfc(_sfc_main$4, [['__scopeId',"data-v-204205aa"]]);

const _sfc_main$3 = {
  __name: 'TestOne',
  __ssrInlineRender: true,
  setup(__props) {


return (_ctx, _push, _parent, _attrs) => {
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "test-one" }, _attrs))} data-v-7e1f10a1><header data-v-7e1f10a1><h2 data-v-7e1f10a1>Counter Demo</h2><p data-v-7e1f10a1>Use the interactive counter below to verify hydration.</p></header>`);
  _push(ssrRenderComponent(TestComponent, null, null, _parent));
  _push(`</section>`);
}
}

};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext()
  ;(ssrContext.modules || (ssrContext.modules = new Set())).add("pages/TestOne.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : undefined
};
const TestOne = /*#__PURE__*/_export_sfc(_sfc_main$3, [['__scopeId',"data-v-7e1f10a1"]]);

const _sfc_main$2 = {
  __name: 'TestTwo',
  __ssrInlineRender: true,
  setup(__props) {

const items = ref(['Alpha', 'Bravo', 'Charlie', 'Delta']);
const highlighted = ref(false);

function toggleHighlight() {
  highlighted.value = !highlighted.value;
}

return (_ctx, _push, _parent, _attrs) => {
  _push(`<section${
    ssrRenderAttrs(mergeProps({ class: "test-two" }, _attrs))
  } data-v-c6b1aef6><header data-v-c6b1aef6><h2 data-v-c6b1aef6>Highlight Toggle</h2><p data-v-c6b1aef6>Toggle the highlight state to confirm reactivity.</p></header><ul class="${
    ssrRenderClass({ highlighted: highlighted.value })
  }" data-v-c6b1aef6><!--[-->`);
  ssrRenderList(items.value, (item, index) => {
    _push(`<li data-v-c6b1aef6>${ssrInterpolate(item)}</li>`);
  });
  _push(`<!--]--></ul>`);
  _push(ssrRenderComponent(TestButton, {
    label: highlighted.value ? 'Remove highlight' : 'Highlight list',
    onClick: toggleHighlight
  }, null, _parent));
  _push(`</section>`);
}
}

};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext()
  ;(ssrContext.modules || (ssrContext.modules = new Set())).add("pages/TestTwo.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : undefined
};
const TestTwo = /*#__PURE__*/_export_sfc(_sfc_main$2, [['__scopeId',"data-v-c6b1aef6"]]);

const _sfc_main$1 = {  };

function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "not-found" }, _attrs))} data-v-4d02daf4><h2 data-v-4d02daf4>Component Not Found</h2><p data-v-4d02daf4>The requested view is unavailable. Please choose another test page.</p></section>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext()
  ;(ssrContext.modules || (ssrContext.modules = new Set())).add("pages/NotFound.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : undefined
};
const NotFound = /*#__PURE__*/_export_sfc(_sfc_main$1, [['ssrRender',_sfc_ssrRender],['__scopeId',"data-v-4d02daf4"]]);

const DEFAULT_PAGE = 'test-one';

const registry = {
  'test-one': TestOne,
  'test-two': TestTwo,
};

function resolvePage(name) {
  return registry[name] ?? NotFound
}

const _sfc_main = {
  __name: "App",
  __ssrInlineRender: true,
  props: {
    pageName: {
      type: String,
      default: DEFAULT_PAGE
    }
  },
  setup(__props) {
    const props = __props;
    {
      console.info("[ssr] rendering page", props.pageName);
    }
    onMounted(() => {
      const container = document.getElementById("reactive");
      const containerPage = container?.dataset.page ?? null;
      console.info("[hydrate] props.pageName", props.pageName, "container dataset", containerPage);
    });
    const resolvedComponent = computed(() => resolvePage(props.pageName));
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<main${ssrRenderAttrs(mergeProps({ class: "page" }, _attrs))} data-v-48dfc755>`);
      ssrRenderVNode(_push, createVNode(resolveDynamicComponent(resolvedComponent.value), null, null), _parent);
      _push(`</main>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("App.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const App = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-48dfc755"]]);

function createApp(pageName = DEFAULT_PAGE) {
  const app = createSSRApp(App, { pageName });
  return { app }
}

async function render(pageName = DEFAULT_PAGE) {
  const { app } = createApp(pageName);
  const ctx = {};
  const html = await renderToString(app, ctx);
  const modules = ctx.modules ? Array.from(ctx.modules) : [];
  return { html, modules }
}

export { render };
