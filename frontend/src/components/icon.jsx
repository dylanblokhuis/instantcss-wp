import { h } from 'preact';
import styled from "styled-components";

const Wrapper = styled.div`
  width: 20px;
  height: 20px;
`;

function Icon({ svg, ...rest }) {
  return (
    <Wrapper dangerouslySetInnerHTML={{ __html: svg }} {...rest} />
  );
}

export default Icon;
