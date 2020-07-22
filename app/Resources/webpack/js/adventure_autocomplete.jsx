import ReactDOM from "react-dom";
import React from "react";
import AsyncCreatableSelect from "react-select/async-creatable";
import AsyncSelect from "react-select/async";
import { debounce } from "./util";

export function render(
  root,
  autocompleteInputs,
  autocompleteSelects,
  searchUrl
) {
  ReactDOM.render(
    <Autocomplete
      elements={[...autocompleteInputs, ...autocompleteSelects]}
      searchUrl={searchUrl}
    />,
    root
  );
}

function Autocomplete({ elements, searchUrl }) {
  return elements.map((element) => {
    const fieldName = element.id.split("_").pop();
    const allowMultiple = element.tagName === "SELECT" && element.multiple;
    const allowCreate =
      element.tagName === "INPUT" || !!element.dataset.allowAdd;

    element.style.display = "none";
    const container = element.parentNode.insertBefore(
      document.createElement("div"),
      element
    );

    const defaultValue = (() => {
      if (element.tagName === "SELECT") {
        return Array.from(element.options)
          .filter((option) => option.selected)
          .map((option) => ({
            value: option.text,
            label: option.text,
          }));
      } else {
        return {
          value: element.value,
          label: element.value,
        };
      }
    })();

    return ReactDOM.createPortal(
      <AutocompleteSelect
        fieldName={fieldName}
        searchUrl={searchUrl}
        allowMultiple={allowMultiple}
        allowCreate={allowCreate}
        defaultValue={defaultValue}
        element={element}
      />,
      container,
      fieldName
    );
  });
}

const AutocompleteSelect = React.memo(function ({
  searchUrl,
  fieldName,
  allowMultiple,
  allowCreate,
  defaultValue,
  element,
}) {
  const [value, setValue] = React.useState(defaultValue);

  const onChange = React.useCallback((options) => {
    setValue(options);
    if (element.tagName === "SELECT") {
      for (const option of element.options) {
        if (
          Array.isArray(options) &&
          options.find(({ value }) => value === option.text)
        ) {
          option.selected = true;
        } else if (options !== null && options.value === option.text) {
          option.selected = true;
        } else {
          option.selected = false;
        }
      }
    } else {
      element.value = options.value;
    }
  });

  const load = React.useCallback(
    debounce((q, cb) => {
      const url =
        searchUrl.replace(/__FIELD__/g, fieldName) +
        `?q=${encodeURIComponent(q)}`;
      fetch(url).then((result) =>
        result
          .json()
          .then((result) =>
            cb(result.map((each) => ({ value: each, label: each })))
          )
      );
    }, 250),
    [fieldName]
  );

  if (allowCreate) {
    return (
      <AsyncCreatableSelect
        cacheOptions={true}
        loadOptions={load}
        defaultOptions={true}
        allowCreateWhileLoading={true}
        isClearable={true}
        isSearchable={true}
        isMulti={allowMultiple}
        closeMenuOnSelect={!allowMultiple}
        blurInputOnSelect={!allowMultiple}
        value={value}
        onChange={onChange}
      />
    );
  } else {
    return (
      <AsyncSelect
        cacheOptions={true}
        loadOptions={load}
        defaultOptions={true}
        isClearable={true}
        isSearchable={true}
        isMulti={allowMultiple}
        closeMenuOnSelect={!allowMultiple}
        blurInputOnSelect={!allowMultiple}
        value={value}
        onChange={onChange}
      />
    );
  }
});
