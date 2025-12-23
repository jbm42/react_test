import styles from './TestButton.module.css'

export default function TestButton({ label = 'Click me', onClick }) {
  function handleClick(event) {
    if (onClick) {
      onClick(event)
    }
  }

  return (
    <button className={styles.button} type="button" onClick={handleClick}>
      {label}
    </button>
  )
}

