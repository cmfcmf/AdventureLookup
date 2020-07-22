import ReactDOM from "react-dom";
import React from "react";
import AsyncCreatableSelect from "react-select/async-creatable";
import AsyncSelect from "react-select/async";

export function render(
  root,
  autocompleteInputs,
  autocompleteSelects,
  searchUrl
) {
  ReactDOM.render(
    <Autocomplete
      autocompleteInputs={autocompleteInputs}
      autocompleteSelects={autocompleteSelects}
      searchUrl={searchUrl}
    />,
    root
  );
}

function Autocomplete({ autocompleteInputs, autocompleteSelects, searchUrl }) {
  return (
    <>
      {autocompleteInputs.map((input) => {
        input.style.display = "none";
        const fieldName = input.id.split("_").pop();
        const container = input.parentNode.insertBefore(
          document.createElement("div"),
          input
        );
        return ReactDOM.createPortal(
          <AutocompleteInput fieldName={fieldName} searchUrl={searchUrl} />,
          container
        );
      })}
      {autocompleteSelects.map((select) => {
        select.style.display = "none";
        const fieldName = select.id.split("_").pop();
        const allowMultiple = select.multiple;
        const allowCreate = !!select.dataset.allowAdd;
        const container = select.parentNode.insertBefore(
          document.createElement("div"),
          select
        );
        return ReactDOM.createPortal(
          <AutocompleteSelect
            fieldName={fieldName}
            searchUrl={searchUrl}
            allowMultiple={allowMultiple}
            allowCreate={allowCreate}
          />,
          container
        );
      })}
    </>
  );
}

function AutocompleteInput({ searchUrl, fieldName }) {
  const load = (q, cb) => {
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
  };

  return (
    <AsyncCreatableSelect
      cacheOptions={true}
      loadOptions={load}
      defaultOptions={true}
      allowCreateWhileLoading={true}
      isClearable={true}
      isSearchable={true}
      isMulti={false}
    />
  );
}

function AutocompleteSelect({
  searchUrl,
  fieldName,
  allowMultiple,
  allowCreate,
}) {
  const load = (q, cb) => {
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
  };

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
      />
    );
  }
}
