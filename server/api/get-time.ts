import { PrismaClient, Prisma } from '@prisma/client'
import axios from 'axios';
import moment from 'moment';

export default defineEventHandler(async (event) => {
  const prisma = new PrismaClient()
  const runtimeConfig = useRuntimeConfig()

  const getTimestamp = async () => {
    const { data } = await axios.get(runtimeConfig.secretEndpoint).then(res => res)

    const timeLeft = data.timeLeft;
    const currentTime = Number.parseInt(moment().format('x'))
  
    return `${currentTime + timeLeft}`
  }

  const getTime = async function() {
    let time = await prisma.time.findFirst();

    if(time !== null) {
      return !moment().isAfter(time.timeToUpdate) ?
        time :
        await prisma.time.update({
          where: {id: time.id},
          data: {
            timestamp: await getTimestamp(),
            timeToUpdate: moment().add(1, 'day').format('YYYY-MM-DD hh:mm:ss')
          }
        })
    }
    
    return await prisma.time.create({
      data: {
        timestamp: await getTimestamp(),
        timeToUpdate: moment().hour(6).add(1, 'day').format('YYYY-MM-DD hh:mm:ss')
      }
    })
  }

  return await getTime()
})
