import { showNotification } from '@mantine/notifications'
import { IconX } from '@tabler/icons-react'
import { rem } from '@mantine/core'

const showError = (message: string, title: string = 'Error!') => {
    return showNotification({
        position: 'top-right',
        color: 'red',
        autoClose: 3000,
        title,
        message,
        icon: <IconX style={{ width: rem(20), height: rem(20) }} />,
    })
}

export { showError }
