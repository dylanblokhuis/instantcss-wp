import { h } from 'preact';
import { useContext, useState } from "preact/hooks";
import styled, { css } from "styled-components";

import Icon from "../icon.jsx";
import folderOpenIcon from "../../icons/folder-open.svg";
import folderIcon from "../../icons/folder.svg";
import sassIcon from "../../icons/sass.svg";
import { AppContext } from "../../app.jsx";

const Row = styled.div`
  padding-left: ${props => props.depth * 10}px;
  cursor: pointer;
  user-select: none;
  
  &:hover {
    background: ${props => props.theme.dark};
  }
`;


const File = ({ file, depth = 0, show = true }) => {
  const [isOpen, setOpen] = useState(true);
  const { setFile } = useContext(AppContext)

  depth++

  function handleClick(file) {
    if (file.is_dir) {
      setOpen(!isOpen);
    } else {
      setFile(file)
    }
  }

  return (
    <div style={{ display: show ? 'block' : 'none'}}>
      <Row onClick={() => handleClick(file)} depth={depth} className="d-flex align-items-center">
        {file.is_dir ? (
          <Icon className="mr-2 py-1" svg={isOpen ? folderOpenIcon : folderIcon} />
        ) : (
          <Icon className="mr-2 py-1" svg={sassIcon} />
        )}

        <span className="flex-grow-1">{file.name}</span>
      </Row>

      {file.children && file.children.map(file => (
        <File key={file.name} depth={depth} file={file} show={isOpen} />
      ))}
    </div>
  );
};

export default File;
