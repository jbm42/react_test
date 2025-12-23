import styles from './TestButton.module.css'

export default function TestButton(props) {
  const handleClick = (event) => {
    if (props.onClick) {
      props.onClick(event)
    }
  }

  return (
    <button class={styles.button} type="button" onClick={handleClick}>
      {props.label ?? 'Click me'}
    </button>
  )
}

