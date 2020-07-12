import { h } from 'preact';
import styled from "styled-components";

const Border = styled.div`
  display: inline-block;
  width: ${props => props.size};
  height: ${props => props.size};
  vertical-align: text-bottom;
  border: .25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  -webkit-animation: spinner-border .75s linear infinite;
  animation: spinner-border .75s linear infinite;
`;

const Inside = styled.span`
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0,0,0,0);
  white-space: nowrap;
  border: 0;
`;

function Spinner({ size = "1.5rem" }) {
  return (
    <Border size={size}>
      <Inside />
    </Border>
  );
}

export default Spinner;
