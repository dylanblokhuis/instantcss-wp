import styled from "styled-components";

const Button = styled.button`
  background: ${props => props.theme.primary};
  border: none;
  color: ${props => props.theme.light};
  padding: 5px 10px;
  outline: none;
  cursor: pointer;
  
  &:hover {
    filter: brightness(110%);
  }
`

export default Button;
