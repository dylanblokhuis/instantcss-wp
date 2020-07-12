import { h } from 'preact';
import { useState } from "preact/hooks";
import styled, { css } from "styled-components";

import Icon from "../icon.jsx";
import folderIconOpen from "../../icons/folder-open.svg";
import sassIcon from "../../icons/sass.svg";

const Row = styled.div`
  padding-left: ${props => props.depth * 10}px;
  cursor: pointer;
  
  &:hover {
    background: ${props => props.theme.dark};
  }
`;


const File = ({ file, depth = 0 }) => {
  const [isOpen, setOpen] = useState(true);

  depth++

  return (
    <div>
      <Row onClick={() => setOpen(!isOpen)} depth={depth} className="d-flex align-items-center">
        {file.is_dir ? (
          <Icon className="mr-2 py-1" svg={folderIconOpen} />
        ) : (
          <Icon className="mr-2 py-1" svg={sassIcon} />
        )}

        <span className="flex-grow-1">{file.name}</span>
      </Row>

      {isOpen && (
        file.children && file.children.map(file => (
          <File key={file.name} depth={depth} file={file} />
        ))
      )}
    </div>
  );
};

export default File;
