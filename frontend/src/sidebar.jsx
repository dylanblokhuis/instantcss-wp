import { h } from 'preact';
import styled from "styled-components";
import useSWR from 'swr';

import api from "./util/api";
import Spinner from "./components/spinner.jsx";
import File from "./components/sidebar/file.jsx";

function Sidebar() {
  const { data: files, error } = useSWR('/files', api)

  if (error) {
    return (
      <div>Something went wrong...</div>
    )
  }

  if (!files) {
    return (
      <div className="d-flex align-items-center justify-content-center h-100">
        <Spinner />
      </div>
    )
  }

  return (
    <div>
      {files.map(file => (
        <File key={file.name} file={file} />
      ))}
    </div>
  );
}

export default Sidebar;
