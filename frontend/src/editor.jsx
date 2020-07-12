import { h } from 'preact';
import MonacoEditor from 'react-monaco-editor';
import { useContext, useEffect, useState } from "preact/hooks";

import { AppContext } from "./app.jsx";
import api from "./util/api";
import Button from "./components/button.jsx";
import Spinner from "./components/spinner.jsx";

const options = {
  fontSize: 14
}

function Editor() {
  const [value, setValue] = useState(null);
  const [isSaving, setSaving] = useState(false);
  const { file } = useContext(AppContext)

  useEffect(() => {
    if (!file) return;

    api("/files/get", {
      method: 'POST',
      body: JSON.stringify({
        path: file.path
      })
    }).then(response => response.content !== undefined && setValue(response.content))
  }, [file])

  function handleSave() {
    setSaving(true);

    api('/files', {
      method: 'PUT',
      body: JSON.stringify({
        path: file.path,
        content: value
      })
    }).then(() => setSaving(false))
  }

  if (value === null) {
    return (
      <div className="h-100 bg-dark d-flex justify-content-center align-items-center font-weight-bold">
        <h1>No file selected</h1>
      </div>
    )
  }

  return (
    <div className="h-100">
      <div className="py-2 px-3 bg-dark d-flex justify-content-between align-items-center">
        <span className="text-white font-weight-bold">{file.path}</span>

        <Button onClick={handleSave}>
          {isSaving ? (
            <span className="d-flex align-items-center">
              <Spinner size="12px" />
              <span className="ml-2">Saving</span>
            </span>
          ) : (
            <span>Save</span>
          )}
        </Button>
      </div>

      <MonacoEditor
        height="100%"
        theme="vs-dark"
        language="scss"
        value={value}
        onChange={(newValue) => setValue(newValue)}
        options={options}
      />
    </div>
  );
}

export default Editor;
